<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;
use App\Models\PresensiDosenModel;
use App\Models\PresensiMahasiswaModel;
use App\Libraries\enums\TipeUser;

class Scan extends BaseController
{
    private bool $WANotificationEnabled;

    protected MahasiswaModel $mahasiswaModel;
    protected DosenModel $dosenModel;
    protected PresensiMahasiswaModel $presensiMahasiswaModel;
    protected PresensiDosenModel $presensiDosenModel;

    public function __construct()
    {
        $this->WANotificationEnabled = getenv('WA_NOTIFICATION') === 'true';

        $this->mahasiswaModel = new MahasiswaModel();
        $this->dosenModel = new DosenModel();
        $this->presensiMahasiswaModel = new PresensiMahasiswaModel();
        $this->presensiDosenModel = new PresensiDosenModel();
    }

    public function index($t = 'Masuk')
    {
        return view('scan/scan', [
            'waktu' => $t,
            'title' => 'Absensi Mahasiswa dan Dosen Berbasis QR Code'
        ]);
    }

    public function cekKode()
    {
        $uniqueCode = $this->request->getVar('unique_code');
        $waktuAbsen = strtolower($this->request->getVar('waktu'));

        // default mahasiswa
        $type = TipeUser::Mahasiswa;
        $result = $this->mahasiswaModel->cekMahasiswa($uniqueCode);

        // jika bukan mahasiswa → cek dosen
        if (empty($result)) {
            $result = $this->dosenModel->cekDosen($uniqueCode);

            if (!empty($result)) {
                $type = TipeUser::Dosen;
            } else {
                return $this->showErrorView('Data tidak ditemukan');
            }
        }

        return match ($waktuAbsen) {
            'masuk'  => $this->absenMasuk($type, $result),
            'pulang' => $this->absenPulang($type, $result),
            default  => $this->showErrorView('Waktu absen tidak valid')
        };
    }

    public function absenMasuk($type, $result)
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        $data = [
            'data'  => $result,
            'waktu' => 'masuk',
            'type'  => $type
        ];

        $message = " sudah absen masuk pada tanggal $date jam $time";

        switch ($type) {
            case TipeUser::Dosen:
                $id = $result['id_dosen'];

                if ($this->presensiDosenModel->cekAbsen($id, $date)) {
                    return $this->showErrorView('Anda sudah absen hari ini', $data);
                }

                $this->presensiDosenModel->absenMasuk($id, $date, $time);
                $data['presensi'] = $this->presensiDosenModel
                    ->getPresensiByIdDosenTanggal($id, $date);

                $message = $result['nama_dosen'] . ' (NIP ' . $result['nip'] . ')' . $message;
                break;

            case TipeUser::Mahasiswa:
                $id = $result['id_mahasiswa'];

                if ($this->presensiMahasiswaModel->cekAbsen($id, $date)) {
                    return $this->showErrorView('Anda sudah absen hari ini', $data);
                }

                $this->presensiMahasiswaModel
                    ->absenMasuk($id, $date, $time, $result['id_matkul']);

                $data['presensi'] = $this->presensiMahasiswaModel
                    ->getPresensiByIdMahasiswaTanggal($id, $date);

                $message = 'Mahasiswa ' . $result['nama_mahasiswa'] .
                    ' (NIM ' . $result['nim'] . ')' . $message;
                break;
        }

        $this->sendWA($result, $message);

        return view('scan/scan-result', $data);
    }

    public function absenPulang($type, $result)
    {
        $date = Time::today()->toDateString();
        $time = Time::now()->toTimeString();

        $data = [
            'data'  => $result,
            'waktu' => 'pulang',
            'type'  => $type
        ];

        $message = " sudah absen pulang pada tanggal $date jam $time";

        switch ($type) {
            case TipeUser::Dosen:
                $id = $result['id_dosen'];
                $presensiId = $this->presensiDosenModel->cekAbsen($id, $date);

                if (!$presensiId) {
                    return $this->showErrorView('Anda belum absen hari ini', $data);
                }

                $this->presensiDosenModel->absenKeluar($presensiId, $time);
                $data['presensi'] = $this->presensiDosenModel->getPresensiById($presensiId);
                $message = $result['nama_dosen'] . ' (NIP ' . $result['nip'] . ')' . $message;
                break;

            case TipeUser::Mahasiswa:
                $id = $result['id_mahasiswa'];
                $presensiId = $this->presensiMahasiswaModel->cekAbsen($id, $date);

                if (!$presensiId) {
                    return $this->showErrorView('Anda belum absen hari ini', $data);
                }

                $this->presensiMahasiswaModel->absenKeluar($presensiId, $time);
                $data['presensi'] = $this->presensiMahasiswaModel->getPresensiById($presensiId);
                $message = 'Mahasiswa ' . $result['nama_mahasiswa'] .
                    ' (NIM ' . $result['nim'] . ')' . $message;
                break;
        }

        $this->sendWA($result, $message);

        return view('scan/scan-result', $data);
    }

    private function sendWA($result, string $message)
    {
        if (!$this->WANotificationEnabled || empty($result['no_hp'])) {
            return;
        }

        try {
            $this->sendNotification([
                'destination' => $result['no_hp'],
                'message'     => $message,
                'delay'       => 0
            ]);
        } catch (\Throwable $e) {
            log_message('error', $e->getMessage());
        }
    }

    public function showErrorView(string $msg, array $data = [])
    {
        $data['msg'] = $msg;
        return view('scan/error-scan-result', $data);
    }
}