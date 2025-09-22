<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Session\Session;
// use Myth\Auth\Config\Auth as AuthConfig;
use App\Models\UserModel;
use App\Entities\User;
use App\Models\ProvinceModel;
use App\Models\RegenciesModel;
use App\Models\PenelitianModel;
use App\Models\InstansiModel;
use App\Models\JurusanModel;

class Auth extends Controller
{
    /**
     * Analysis assist; remove after CodeIgniter 4.3 release.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    protected $auth;
    protected $provinsiModel;
    protected $regenciesModel;
    protected $penelitianModel;
    protected $instansiModel;
    protected $jurusanModel;


    /**
     * @var AuthConfig
     */
    protected $config;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Most services in this controller require
        // the session to be started - so fire it up!
        $this->session = service('session');

        $this->config = config('Auth');
        $this->auth   = service('authentication');
        $this->provinsiModel = new ProvinceModel();
        $this->regenciesModel = new RegenciesModel();
        $this->penelitianModel = new PenelitianModel();
        $this->instansiModel = new InstansiModel();
        $this->jurusanModel = new JurusanModel();
    }

    // --------------------------------------------------------------------
    // Login/out
    // --------------------------------------------------------------------
    /**
     * Displays the login form, or redirects
     * the user to their destination/home if
     * they are already logged in.
     *
     * @return RedirectResponse|string
     */
    public function login()
    {
        // No need to show a login form if the user
        // is already logged in.
        if ($this->auth->check()) {
            $redirectURL = session('redirect_url') ?? site_url($this->config->landingRoute);
            unset($_SESSION['redirect_url']);

            return redirect()
                ->to($redirectURL);
        }

        // Set a return URL if none is specified.
        $_SESSION['redirect_url'] = session('redirect_url') ?? previous_url();

        // Display the login view.
        return $this->_render($this->config->views['login'], ['config' => $this->config]);
    }

    /**
     * Attempts to verify the user's credentials
     * through a POST request.
     *
     * @return RedirectResponse
     */
    public function attemptLogin()
    {
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        // Determine credential type
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Try to log them in...
        if (! $this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
        }

        // Is the user being forced to reset their password?
        if ($this->auth->user()->force_pass_reset === true) {
            $url = route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash;

            return redirect()
                ->to($url)
                ->withCookies();
        }

        // Ambil user yang login
        $user = $this->auth->user();
        if (in_array('admin', $user->getRoles())) {
            $redirectURL = site_url('/manage-pendaftaran');
        } else {
            $redirectURL = session('redirect_url') ?? site_url($this->config->landingRoute);
        }
        unset($_SESSION['redirect_url']);

        return redirect()
            ->to($redirectURL)
            ->withCookies()
            ->with('message', lang('Auth.loginSuccess'));
    }

    /**
     * Log the user out.
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        if ($this->auth->check()) {
            $this->auth->logout();
        }

        return redirect()->to(site_url('/'));
    }

    // --------------------------------------------------------------------
    // Register
    // --------------------------------------------------------------------
    /**
     * Displays the user registration page.
     *
     * @return RedirectResponse|string
     */
    public function register()
    {
        // check if already logged in.
        if ($this->auth->check()) {
            return redirect()->back();
        }

        // Check if registration is allowed
        if (! $this->config->allowRegistration) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }
        $data['instansi'] =$this->instansiModel->findAll();

        $data['jurusan'] =$this->jurusanModel->findAll();
        
        $data['provinsi'] = $this->provinsiModel->select('id,province')->orderBy('id')->findAll();
        $data['config']   = $this->config;

        return $this->_render($this->config->views['register'], $data);
    }

    public function getCities()
    {
        $provinsi_id = $this->request->getVar('provinsi');
        $searchTerm = $this->request->getVar('searchTerm');

        $provinsi_id = $this->request->getVar('provinsi');
        if (!$provinsi_id) {
            return $this->response->setJSON(['data' => []]);
        }

        $query = $this->regenciesModel->select('id, regency')
                                ->where('province_id', $provinsi_id);

        if ($searchTerm) {
            $query->like('regency', $searchTerm);
        }

        $listCities = $query->orderBy('id')->findAll();

        $data = [];
        foreach ($listCities as $value) {
            $data[] = [
                'id' => $value['id'],
                'text' => $value['regency'],
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function getInstansi()
    {
        $kelompok = $this->request->getGet('kelompok'); // 'smk' atau 'pt'

        $model = new InstansiModel();


        if ($kelompok === 'smk') {
            $data = $model->where('tingkat', $kelompok)->findAll();
        } else {
            $data = $model->where('tingkat', $kelompok)->findAll();
        }

        return $this->response->setJSON($data);
    }

    /**
     * Attempt to register a new user.
     *
     * @return RedirectResponse
     */
   

    public function attemptRegister()
    {
        // Check if registration is allowed
        if (! $this->config->allowRegistration) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', lang('Auth.registerDisabled'));
        }

        $users = model(UserModel::class);

        // Validate fields
        $rules = config('Validation')->registrationRules ?? [
            // 'username' => 'required|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Validate passwords
        $rules = [
            'password' => 'required|min_length[8]|regex_match[/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+/]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Upload file dokumen
        $fullname = $this->request->getPost('nama');

        $user_image = $this->_uploadFile('user_image', 'uploads/user_image', $fullname, 'user_image');
        $cvName = $this->_uploadFile('cv', 'uploads/cv', $fullname, 'cv');
        $proposalName = $this->_uploadFile('proposal', 'uploads/proposal', $fullname, 'proposal');
        $suratName = $this->_uploadFile('surat_permohonan', 'uploads/surat_permohonan', $fullname, 'surat-permohonan');
        $ktpName = $this->_uploadFile('ktp_kk', 'uploads/ktp_kk', $fullname, 'ktp-kk');
       
        
        // Data user
        $allowedPostFields = array_merge(['password'], $this->config->validFields, $this->config->personalFields);
        $userData = $this->request->getPost($allowedPostFields);

        $userData = array_merge($userData, [
            'fullname' => $this->request->getPost('nama'),
            'user_image' => $user_image,
            'no_hp' => $this->request->getPost('no_hp'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'nisn_nim' => $this->request->getPost('nisn_nim'),
            'alamat' => $this->request->getPost('alamat'),
            'province_id' => $this->request->getPost('provinsi'),
            'city_id' => $this->request->getPost('kota'),
            'domisili' => $this->request->getPost('domisili'),
            'provinceDom_id' => $this->request->getPost('provinsiDom'),
            'cityDom_id' => $this->request->getPost('kotaDom'),
            'instansi_id' => $this->request->getPost('instansi'),
            'jurusan_id' => $this->request->getPost('jurusan'),
            'tingkat_pendidikan' => $this->request->getPost('jenjang_pendidikan'),
            'semester' => $this->request->getPost('semester'),
            'nilai_ipk' => $this->request->getPost('nilai_ipk'),
            'cv' => $cvName,
            'proposal' => $proposalName,
            'surat_permohonan' => $suratName,
            'no_surat' => $this->request->getPost('no_surat'),
            'tanggal_surat' => $this->request->getPost('tanggal_surat'),
            'nama_pimpinan' => $this->request->getPost('nama_pimpinan'),
            'jabatan' => $this->request->getPost('jabatan'),
            'email_instansi' => $this->request->getPost('email_instansi'),
            'ktp_kk' => $ktpName,
        ]);

        $user = new User($userData);

        $this->config->requireActivation === null ? $user->activate() : $user->generateActivateHash();

        // Default group
        if (! empty($this->config->defaultUserGroup)) {
            $users = $users->withGroup($this->config->defaultUserGroup);
        }

        if (! $users->save($user)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $users->errors());
        }


        if ($this->config->requireActivation !== null) {
            $activator = service('activator');
            $sent      = $activator->send($user);

            if (! $sent) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $activator->error() ?? lang('Auth.unknownError'));
            }

            return redirect()
                ->route('login')
                ->with('message', lang('Auth.activationSuccess'));
        }

        return redirect()
            ->route('login')
            ->with('message', lang('Auth.registerSuccess'));
    }

    private function _uploadFile($field, $path, $fullname, $type)
    {
        $file = $this->request->getFile($field);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Slugify nama
            $slugName = url_title($fullname, '-', true);
            $typeSlug = url_title($type, '-', true);
            $randomNumber = random_int(1000, 9999);
            $ext = $file->getExtension();
            $newName = "{$slugName}-{$typeSlug}-{$randomNumber}.{$ext}";

            $file->move($path, $newName);
            return $newName;
        }
        return null;
    }



    // --------------------------------------------------------------------
    // Forgot Password
    // --------------------------------------------------------------------
    /**
     * Displays the forgot password form.
     *
     * @return RedirectResponse|string
     */
    public function forgotPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        return $this->_render($this->config->views['forgot'], ['config' => $this->config]);
    }

    /**
     * Attempts to find a user account with that password
     * and send password reset instructions to them.
     *
     * @return RedirectResponse
     */
    public function attemptForgot()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        $rules = [
            'email' => [
                'label' => lang('Auth.emailAddress'),
                'rules' => 'required|valid_email',
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $users = model(UserModel::class);

        $user = $users->where('email', $this->request->getPost('email'))->first();

        if (null === $user) {
            return redirect()
                ->back()
                ->with('error', lang('Auth.forgotNoUser'));
        }

        // Save the reset hash /
        $user->generateResetHash();
        $users->save($user);

        $resetter = service('resetter');
        $sent     = $resetter->send($user);

        if (! $sent) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $resetter->error() ?? lang('Auth.unknownError'));
        }

        return redirect()
            ->route('reset-password')
            ->with('message', lang('Auth.forgotEmailSent'));
    }

    /**
     * Displays the Reset Password form.
     *
     * @return RedirectResponse|string
     */
    public function resetPassword()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        $token = $this->request->getGet('token');

        return $this->_render($this->config->views['reset'], [
            'config' => $this->config,
            'token'  => $token,
        ]);
    }

    /**
     * Verifies the code with the email and saves the new password,
     * if they all pass validation.
     *
     * @return RedirectResponse
     */
    public function attemptReset()
    {
        if ($this->config->activeResetter === null) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.forgotDisabled'));
        }

        $users = model(UserModel::class);

        // First things first - log the reset attempt.
        $users->logResetAttempt(
            $this->request->getPost('email'),
            $this->request->getPost('token'),
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        $rules = [
            'token'        => 'required',
            'email'        => 'required|valid_email',
            'password'     => 'required|required|min_length[8]|regex_match[/(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+/]',
            'pass_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user = $users->where('email', $this->request->getPost('email'))
            ->where('reset_hash', $this->request->getPost('token'))
            ->first();

        if (null === $user) {
            return redirect()
                ->back()
                ->with('error', lang('Auth.forgotNoUser'));
        }

        // Reset token still valid?
        if (! empty($user->reset_expires) && time() > $user->reset_expires->getTimestamp()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', lang('Auth.resetTokenExpired'));
        }

        // Success! Save the new password, and cleanup the reset hash.
        $user->password         = $this->request->getPost('password');
        $user->reset_hash       = null;
        $user->reset_at         = date('Y-m-d H:i:s');
        $user->reset_expires    = null;
        $user->force_pass_reset = false;
        $users->save($user);

        return redirect()
            ->route('login')
            ->with('message', lang('Auth.resetSuccess'));
    }

    /**
     * Activate account.
     *
     * @return mixed
     */
    public function activateAccount()
    {
        $users = model(UserModel::class);

        // First things first - log the activation attempt.
        $users->logActivationAttempt(
            $this->request->getGet('token'),
            $this->request->getIPAddress(),
            (string) $this->request->getUserAgent()
        );

        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $user = $users->where('activate_hash', $this->request->getGet('token'))
            ->where('active', 0)
            ->first();

        if (null === $user) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.activationNoUser'));
        }

        $user->activate();

        $users->save($user);

        return redirect()
            ->route('login')
            ->with('message', lang('Auth.registerSuccess'));
    }

    /**
     * Resend activation account.
     *
     * @return mixed
     */
    public function resendActivateAccount()
    {
        if ($this->config->requireActivation === null) {
            return redirect()
                ->route('login');
        }

        $throttler = service('throttler');

        if ($throttler->check(md5($this->request->getIPAddress()), 2, MINUTE) === false) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(lang('Auth.tooManyRequests', [$throttler->getTokentime()]));
        }

        $login = urldecode($this->request->getGet('login'));
        $type  = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $users = model(UserModel::class);

        $user = $users->where($type, $login)
            ->where('active', 0)
            ->first();

        if (null === $user) {
            return redirect()
                ->route('login')
                ->with('error', lang('Auth.activationNoUser'));
        }

        $activator = service('activator');
        $sent      = $activator->send($user);

        if (! $sent) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $activator->error() ?? lang('Auth.unknownError'));
        }

        // Success!
        return redirect()
            ->route('login')
            ->with('message', lang('Auth.activationSuccess'));
    }

    /**
     * Render the view.
     *
     * @return string
     */
    protected function _render(string $view, array $data = [])
    {
        return view($view, $data);
    }
}
