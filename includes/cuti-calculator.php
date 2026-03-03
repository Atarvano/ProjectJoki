<?php
/**
 * Cuti Calculator Engine
 * 
 * Deterministic engine for 8-year leave entitlement calculation.
 * 
 * @package SicutiHrd
 */

/**
 * Validasi input tahun bergabung
 *
 * @param int|string $tahun
 * @return array ['valid' => bool, 'pesan' => string]
 */
function validasiTahunBergabung($tahun) {
    // Basic validation: Check if empty or not numeric
    if (empty($tahun) || !is_numeric($tahun)) {
        return [
            'valid' => false,
            'pesan' => 'Mohon masukkan tahun yang valid (contoh: 2020).'
        ];
    }
    
    $tahun = (int)$tahun;
    $tahun_sekarang = (int)date('Y');
    
    // Range check: 1990 to Current Year
    if ($tahun < 1990 || $tahun > $tahun_sekarang) {
        return [
            'valid' => false,
            'pesan' => "Tahun harus antara 1990 dan $tahun_sekarang."
        ];
    }
    
    return [
        'valid' => true,
        'pesan' => ''
    ];
}

/**
 * Hitung hak cuti selama 8 tahun periode
 *
 * @param int $tahunBergabung
 * @return array
 */
function hitungHakCuti($tahunBergabung) {
    $tahunBergabung = (int)$tahunBergabung;
    $tahunMulai = $tahunBergabung + 1; // Hak cuti dimulai tahun berikutnya
    $tahunSelesai = $tahunMulai + 7;   // Total 8 tahun
    $tahunSekarang = (int)date('Y');
    
    $data = [];
    
    // Pola cuti tahun 1-6 (incrementing pattern)
    // Tahun 1-2: 12 hari
    // Tahun 3-4: 14 hari
    // Tahun 5-6: 16 hari
    // Tahun 7-8: 6 hari (Locked requirement)
    $polaCuti = [
        1 => 12,
        2 => 12,
        3 => 14,
        4 => 14,
        5 => 16,
        6 => 16,
        7 => 6,
        8 => 6
    ];
    
    for ($i = 1; $i <= 8; $i++) {
        $tahunKalender = $tahunMulai + ($i - 1);
        $jumlahCuti = $polaCuti[$i];
        
        // Tentukan status berdasarkan tahun kalender vs tahun sekarang
        if ($tahunKalender < $tahunSekarang) {
            $status = "Tersedia"; // Historical/Available (assuming not taken for simplicity in this view)
            $statusClass = "bg-success";
        } elseif ($tahunKalender == $tahunSekarang) {
            $status = "Menunggu"; // Current year entitlement
            $statusClass = "bg-warning text-dark";
        } else {
            $status = "Rencana"; // Future entitlement
            $statusClass = "bg-secondary";
        }
        
        $data[] = [
            'tahun_ke' => $i,
            'tahun_kalender' => $tahunKalender,
            'hari_cuti' => $jumlahCuti,
            'status' => $status,
            'status_class' => $statusClass
        ];
    }
    
    return [
        'tahun_bergabung' => $tahunBergabung,
        'tahun_mulai' => $tahunMulai,
        'tahun_selesai' => $tahunSelesai,
        'data' => $data
    ];
}
