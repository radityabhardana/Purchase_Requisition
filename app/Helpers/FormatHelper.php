<?php
/**
 * Formatting & Presentation Helper
 * Sistem Informasi Purchasing & Inventaris PT Nandya Karya Perkasa
 */

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format angka menjadi format mata uang Rupiah (IDR)
     */
    public static function rupiah(float|int|string|null $angka): string
    {
        $num = (float)($angka ?? 0);
        return 'Rp ' . number_format($num, 0, ',', '.');
    }

    /**
     * Format tanggal standar MySQL (YYYY-MM-DD) ke format bahasa Indonesia
     */
    public static function dateIndo(?string $tanggal, bool $withTime = false): string
    {
        if (empty($tanggal) || $tanggal === '0000-00-00') {
            return '-';
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $time = strtotime($tanggal);
        if (!$time) {
            return $tanggal;
        }

        $tgl = (int)date('d', $time);
        $bln = (int)date('m', $time);
        $thn = date('Y', $time);

        $result = sprintf('%02d %s %s', $tgl, $bulan[$bln] ?? '', $thn);

        if ($withTime) {
            $result .= ' ' . date('H:i', $time) . ' WIB';
        }

        return $result;
    }

    /**
     * Generate badge HTML status untuk dokumen PR dan PO (Bootstrap 5.3)
     */
    public static function statusBadge(string $status): string
    {
        return match ($status) {
            'Pending' => '<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-warning p-1 rounded-circle"> </span>Pending</span>',
            'Approved' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-success p-1 rounded-circle"> </span>Approved</span>',
            'Rejected' => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-danger p-1 rounded-circle"> </span>Rejected</span>',
            'PO Issued' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-primary p-1 rounded-circle"> </span>PO Issued</span>',
            'Issued' => '<span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-info p-1 rounded-circle"> </span>PO Terbit</span>',
            'Partial Received' => '<span class="badge bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-secondary p-1 rounded-circle"> </span>Sebagian Tiba</span>',
            'Completed' => '<span class="badge rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5" style="background-color: #ccfbf1; color: #0f766e; border: 1px solid #99f6e4;"><span class="badge p-1 rounded-circle" style="background-color: #0f766e;"> </span>Selesai (Closed)</span>',
            'Shipped' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-primary p-1 rounded-circle"> </span>Sedang Dikirim</span>',
            'Delivered' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 small fw-semibold d-inline-flex align-items-center gap-1.5"><span class="badge bg-success p-1 rounded-circle"> </span>Diterima (Selesai)</span>',
            'Draft' => '<span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 small fw-semibold">Draft</span>',
            'Cancelled' => '<span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1 small fw-semibold">Dibatalkan</span>',
            default => '<span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 small fw-semibold">' . htmlspecialchars($status) . '</span>',
        };
    }

    /**
     * Generate badge HTML tingkat urgensi (Bootstrap 5.3)
     */
    public static function priorityBadge(string $priority): string
    {
        return match ($priority) {
            'Emergency' => '<span class="badge bg-danger text-white fw-bold px-2 py-1 shadow-sm">EMERGENCY</span>',
            'Urgent' => '<span class="badge bg-warning text-dark fw-bold px-2 py-1 shadow-sm">Urgent</span>',
            'Normal' => '<span class="badge bg-light text-secondary border px-2 py-1">Normal</span>',
            default => htmlspecialchars($priority),
        };
    }
}
