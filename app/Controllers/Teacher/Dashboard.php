<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\MatkulModel;
use App\Models\MahasiswaModel;
use App\Models\PresensiMahasiswaModel;
use CodeIgniter\I18n\Time;

use App\Models\KehadiranModel;

class Dashboard extends BaseController
{
    protected MatkulModel $matkulModel;
    protected MahasiswaModel $mahasiswaModel;
    protected PresensiMahasiswaModel $presensiMahasiswaModel;
    protected KehadiranModel $kehadiranModel;

    public function __construct()
    {
        $this->matkulModel = new MatkulModel();
        $this->mahasiswaModel = new MahasiswaModel();
        $this->presensiMahasiswaModel = new PresensiMahasiswaModel();
        $this->kehadiranModel = new KehadiranModel();
    }

    public function index()
    {
        $user = user();
        if (empty($user->id_guru)) {
            return redirect()->to('admin')->with('error', 'Anda bukan Dosen Pengampu Mata Kulaih.');
        }

        // Get class where the teacher is Wali Kelas
        $matkul = $this->matkulModel->getKelasByWali($user->id_dosen);

        if (empty($matkul)) {
            $data = [
                'title' => 'Dashboard Dosen',
                'ctx' => 'dashboard',
                'no_class' => true
            ];
            return view('teacher/dashboard', $data);
        }

        $now = Time::now();
        $today = $now->toDateString();

        // Basic stats
        $data = [
            'title' => 'Dashboard Dosen',
            'ctx' => 'dashboard',
            'matkul' => $matkul,
            'summary' => [
                'total_mahasiswa' => $this->mahasiswaModel->where('id_matkul', $matkul['id_matkul'])->countAllResults(),
                'hadir_hari_ini' => $this->presensiMahasiswaModel->where(['id_matkul' => $matkul['id_matkul'], 'tanggal' => $today, 'id_kehadiran' => '1'])->countAllResults(),
                'sakit_hari_ini' => $this->presensiMahasiswaModel->where(['id_matkul' => $matkul['id_matkul'], 'tanggal' => $today, 'id_kehadiran' => '2'])->countAllResults(),
                'izin_hari_ini' => $this->presensiMahasiswaModel->where(['id_matkul' => $matkul['id_matkul'], 'tanggal' => $today, 'id_kehadiran' => '3'])->countAllResults(),
                'alfa_hari_ini' => $this->presensiMahasiswaModel->where(['id_matkul' => $matkul['id_matkul'], 'tanggal' => $today, 'id_kehadiran' => '4'])->countAllResults(),
            ]
        ];

        // Weekly chart data
        $dateRange = [];
        $kehadiranArray = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->subDays($i)->toDateString();
            if ($i == 0) {
                $formattedDate = "Hari ini";
            } else {
                $t = $now->subDays($i);
                $formattedDate = "{$t->getDay()} " . substr($t->toFormattedDateString(), 0, 3);
            }
            array_push($dateRange, $formattedDate);
            array_push(
                $kehadiranArray,
                $this->presensiMahasiswaModel->where(['id_matkul' => $matkul['id_matkul'], 'tanggal' => $date, 'id_kehadiran' => '1'])->countAllResults()
            );
        }

        $data['dateRange'] = $dateRange;
        $data['kehadiranArray'] = $kehadiranArray;

        return view('teacher/dashboard', $data);
    }
    /**
     * Show attendance management page for the Wali Kelas.
     */
    public function attendance()
    {
        $user = user();
        if (empty($user->id_dosen)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Anda bukan Dosen Pengampu Mata Kuliah.');
        }

        $matkul = $this->matkulModel->getKelasByDosen($user->id_dosen);
        if (empty($matkul)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Anda belum ditugaskan sebagai Dosen.');
        }

        $data = [
            'title' => 'Manajemen Kehadiran',
            'ctx' => 'attendance',
            'matkul' => $matkul,
            'date' => Time::now()->toDateString()
        ];

        return view('teacher/attendance', $data);
    }

    public function getAttendanceList()
    {
        $idmatkul = $this->request->getVar('id_matkul');
        $namamatkul = $this->request->getVar('matkul'); // Just passed back to view
        $tanggal = $this->request->getVar('tanggal');

        $result = $this->presensiMahasiswaModel->getPresensiByMatkulTanggal($idmatkul, $tanggal);
        $lewat = Time::parse($tanggal)->isAfter(Time::today());

        $data = [
            'data' => $result,
            'matkul' => $namamatkul,
            'lewat' => $lewat
        ];

        return view('teacher/absen/list_absen_mahasiswa', $data);
    }

    public function getEditModal()
    {
        $idPresensi = $this->request->getVar('id_presensi');
        $idMahasiswa = $this->request->getVar('id_mahasiswa');

        $data = [
            'presensi' => $this->presensiMahasiswaModel->getPresensiById($idPresensi),
            'listKehadiran' => $this->kehadiranModel->getAllKehadiran(),
            'data' => $this->mahasiswaModel->getMahasiswaById($idMahasiswa)
        ];

        return view('teacher/absen/modal_ubah_kehadiran', $data);
    }

    public function updateSingleAttendance()
    {
        $idKehadiran = $this->request->getVar('id_kehadiran');
        $idMahasiswa = $this->request->getVar('id_mahasiswa');
        $idMatkul = $this->request->getVar('id_matkul');
        $tanggal = $this->request->getVar('tanggal');
        $jamMasuk = $this->request->getVar('jam_masuk');
        $jamKeluar = $this->request->getVar('jam_keluar');
        $keterangan = $this->request->getVar('keterangan');

        // Check if attendance exists
        $cek = $this->presensiMahasiswaModel->cekAbsen($idMahasiswa, $tanggal);

        // Update or Insert (updatePresensi handles logic if first arg is ID or null/false)
        /* 
           wait, presensiSiswaModel->updatePresensi(idPresensi, ...)
           cekAbsen returns ID if exists, OR false.
           If false, we pass null to create new.
        */
        $result = $this->presensiMahasiswaModel->updatePresensi(
            $cek == false ? null : $cek,
            $idMahasiswa,
            $idMatkul,
            $tanggal,
            $idKehadiran,
            $jamMasuk ?: null,
            $jamKeluar ?: null,
            $keterangan
        );

        $response['nama_mahasiswa'] = $this->mahasiswaModel->getMahasiswaById($idMahasiswa)['nama_mahasiswa'];
        $response['status'] = $result ? true : false;

        return $this->response->setJSON($response);
    }
}
