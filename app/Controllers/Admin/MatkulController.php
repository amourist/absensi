<?php

namespace App\Controllers\Admin;

use App\Models\JurusanModel;
use App\Models\MatkulModel;
use App\Controllers\BaseController;

class MatkulController extends BaseController
{
    protected MatkulModel $matkulModel;
    protected JurusanModel $jurusanModel;
    protected \App\Models\DosenModel $dosenModel;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->matkulModel = new MatkulModel();
        $this->jurusanModel = new JurusanModel();
        $this->dosenModel = new \App\Models\DosenModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        if (user()->toArray()['is_superadmin'] != '1') {
            return redirect()->to('admin');
        }


        $data = [
            'title' => 'Matkul & Jurusan',
            'ctx' => 'matkul',
        ];

        return view('admin/matkul/index', $data);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function listData()
    {
        $vars['data'] = $this->matkulModel->getDataMatkul();
        $htmlContent = '';
        if (!empty($vars['data'])) {
            $htmlContent = view('admin/matkul/list-matkul', $vars);
            $data = [
                'result' => 1,
                'htmlContent' => $htmlContent,
            ];
            echo json_encode($data);
        } else {
            echo json_encode(['result' => 0]);
        }
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function tambahMatkul()
    {
        $data['ctx'] = 'matkul';
        $data['title'] = 'Tambah Data Matkul';
        $data['jurusan'] = $this->jurusanModel->findAll();
        $data['dosen'] = $this->dosenModel->getAllDosen();

        return view('/admin/matkul/create', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function tambahMatkulPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('tingkat', 'Tingkat', 'required|max_length[10]');
        $val->setRule('id_jurusan', 'Jurusan', 'required|numeric');
        $val->setRule('index_matkul', 'Index', 'required|max_length[5]');
        $val->setRule('id_dosbing', 'Dosen Pembimbing', 'permit_empty|numeric');

        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->to('admin/matkul/tambah')->withInput();
        } else {
            if ($this->matkulModel->addMatkul()) {
                $this->session->setFlashdata('success', 'Tambah data berhasil');
                return redirect()->to('admin/matkul');
            } else {
                $this->session->setFlashdata('error', 'Gagal menambah data');
                return redirect()->to('admin/matkul/tambah')->withInput();
            }
        }
    }

    /**
     * Return a resource object, with default properties
     *
     * @return mixed
     */
    public function editMatkul($id)
    {
        $data['title'] = 'Edit Matkul';
        $data['ctx'] = 'matkul';
        $data['jurusan'] = $this->jurusanModel->findAll();
        $data['dosen'] = $this->dosenModel->getAllDosen();
        $data['matkul'] = $this->matkulModel->getMatkul($id);
        if (empty($data['matkul'])) {
            return redirect()->to('admin/matkul');
        }

        return view('/admin/matkul/edit', $data);
    }

    /**
     * Edit a resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function editMatkulPost()
    {
        $val = \Config\Services::validation();
        $val->setRule('tingkat', 'Tingkat', 'required|max_length[10]');
        $val->setRule('id_jurusan', 'Jurusan', 'required|numeric');
        $val->setRule('index_matkul', 'Index', 'required|max_length[5]');
        $val->setRule('id_dosbing', 'Dosen Pembimbing', 'permit_empty|numeric');
        if (!$this->validate(getValRules($val))) {
            $this->session->setFlashdata('errors', $val->getErrors());
            return redirect()->back();
        } else {
            $id = inputPost('id');
            if ($this->matkulModel->editMatkul($id)) {
                $this->session->setFlashdata('success', 'Edit data berhasil');
                return redirect()->to('admin/matkul');
            } else {
                $this->session->setFlashdata('error', 'Gagal Mengubah data');
            }
        }
        return redirect()->to('admin/matkul/edit/' . cleanNumber($id));
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function deleteMatkulPost($id = null)
    {
        $id = inputPost('id');
        $matkul = $this->matkulModel->getMatkul($id);
        if (!empty($matkul)) {
            $mahasiswaModel = new \App\Models\MahasiswaModel();
            if (!empty($mahasiswaModel->getMahasiswaCountByMatkul($id))) {
                $this->session->setFlashdata('error', 'Matkul Masih Memiliki Mahasiswa Aktif');
                exit();
            }
            if ($this->matkulModel->deleteMatkul($id)) {
                $this->session->setFlashdata('success', 'Data berhasil dihapus');
            } else {
                $this->session->setFlashdata('error', 'Gagal menghapus data');
            }
        }
    }
}
