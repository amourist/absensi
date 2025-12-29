<?php

namespace App\Controllers\Teacher;

use App\Controllers\BaseController;
use App\Models\MatkulModel;
use App\Models\MahasiswaModel;
use App\Controllers\Admin\QRGenerator;

class QRCode extends BaseController
{
    protected MatkulModel $matkulModel;
    protected MahasiswaModel $mahasiswaModel;

    public function __construct()
    {
        $this->matkulModel = new MatkulModel();
        $this->mahasiswaModel = new MahasiswaModel();
    }

    public function index()
    {
        $user = user();
        if (empty($user->id_dosen)) {
            return redirect()->to('admin')->with('error', 'Anda bukan Dosen Pengampu.');
        }

        $matkul = $this->matkulModel->getMatkulByDosen($user->id_dosen);

        if (empty($matkul)) {
            return redirect()->to('teacher/dashboard')->with('error', 'Mata Kuliah belum ditugaskan.');
        }

        $mahasiswa = $this->mahasiswaModel->getSiswaByKelas($matkul['id_matkul']);

        $data = [
            'title' => 'Download QR Code Mahasiswa',
            'ctx' => 'qr',
            'matkul' => $matkul,
            'mahasiswa' => $mahasiswa
        ];

        return view('teacher/qr_code', $data);
    }

    public function download()
    {
        $user = user();
        if (empty($user->id_dosen)) {
            return redirect()->to('admin')->with('error', 'Anda bukan Dosen Pengampu.');
        }

        $matkul = $this->matkulModel->getKelasByWali($user->id_dosen);

        if (empty($matkul)) {
            return redirect()->back();
        }

        // We can reuse the admin QR generator logic
        $qrGenerator = new QRGenerator();
        $qrGenerator->initController($this->request, $this->response, service('logger'));

        $this->request->setGlobal('get', ['id_matkul' => $matkul['id_matkul']]);

        return $qrGenerator->downloadAllQrSiswa();
    }
}
