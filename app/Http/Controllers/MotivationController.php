<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MotivationController extends Controller
{
    public function random(): JsonResponse
    {
        try {
            $sentences = (function (): array {
                $spreadsheetId = config('services.google_sheets.motivation_spreadsheet_id');
                $gid = config('services.google_sheets.motivation_gid', '0');

                if (! is_string($spreadsheetId) || $spreadsheetId === '') {
                    throw new RuntimeException('ID Google Sheets belum dikonfigurasi.');
                }

                $url = sprintf(
                    'https://docs.google.com/spreadsheets/d/%s/export?format=csv&gid=%s',
                    rawurlencode($spreadsheetId),
                    rawurlencode((string) $gid),
                );

                $caBundle = config('services.google_sheets.ca_bundle');

                if (! is_string($caBundle) || $caBundle === '') {
                    $windowsCaBundle = 'C:\\Program Files\\Git\\usr\\ssl\\certs\\ca-bundle.crt';
                    $caBundle = PHP_OS_FAMILY === 'Windows' && is_readable($windowsCaBundle)
                        ? $windowsCaBundle
                        : null;
                }

                $request = Http::timeout(10);

                if ($caBundle !== null) {
                    if (! is_readable($caBundle)) {
                        throw new RuntimeException('File CA bundle tidak dapat dibaca.');
                    }

                    $request = $request->withOptions(['verify' => $caBundle]);
                }

                $csv = $request->get($url)->throw()->body();
                $sentences = [];

                foreach (preg_split('/\r\n|\n|\r/', $csv) ?: [] as $line) {
                    $row = str_getcsv($line);
                    $sentence = trim($row[0] ?? '');

                    if ($sentence === '') {
                        continue;
                    }

                    if ($sentences === [] && preg_match('/^(motivasi|kalimat|quote|quotes|sentence)$/i', $sentence)) {
                        continue;
                    }

                    $sentences[] = $sentence;
                }

                if ($sentences === []) {
                    throw new RuntimeException('Tidak ada kalimat motivasi pada kolom pertama spreadsheet.');
                }

                return $sentences;
            })();

            return response()->json([
                'motivation' => $sentences[array_rand($sentences)],
            ]);
        } catch (ConnectionException|RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Kalimat motivasi belum dapat dimuat. Periksa akses dan isi Google Sheets, lalu coba lagi.',
            ], 503);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Terjadi kendala saat mengambil kalimat motivasi dari spreadsheet.',
            ], 503);
        }
    }
}
