<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KandidatController;
use App\Http\Controllers\LaporanPerformaceController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\MasterAktifController;
use App\Http\Controllers\MasterKonfirmController;
use App\Http\Controllers\MasterTidakAktifController;
use App\Http\Controllers\MasterTrainingTandemController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PosisiController;
use App\Http\Controllers\AkunUserController;
use App\Http\Controllers\ProsesRekrutmenController;
use App\Http\Controllers\SumberController;
use App\Http\Controllers\TargetJumlahController;
use App\Http\Controllers\WilayahController;
use App\Models\Blacklist;
use App\Models\LogActivity;
use App\Models\MasterAktif;
use App\Models\MasterTrainingTandem;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/login', [AuthController::class,'index'])->name('login');
Route::post('/login', [AuthController::class,'login']);
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

//SUPERADMIN
Route::middleware('auth')->group(function () {
    
Route::get('/dashboard', [DashboardController::class,'superadminindex'])->name('superadmindashboard');
Route::get('/akunuser',[AkunUserController::class,'superadminindex'])->name('superadmin.akunuser.index');
Route::get('/akunuser/create',[AkunUserController::class,'superadmincreate'])->name('superadmin.akunuser.create');
Route::post('/akunuser/post',[AkunUserController::class,'superadminstore'])->name('superadmin.akunuser.store');
Route::get('/akunuser/show/{id}',[AkunUserController::class,'superadminshow'])->name('superadminshowakununser');
Route::post('/akunuser/update/{id}', [AkunUserController::class,'superadminupdate'])->name('superadminupdateuser');
Route::delete('/deleteuser/{id}', [AkunUserController::class, 'superadmindestroy'])->name('superadmindeleteuser');
Route::post('/user/{user}/reset-password', [AkunUserController::class,'resetPassword'])->name('reset-password');
Route::get('/detailposisi/{id}',[AkunUserController::class,'detailposisi'])->name('detailposisi');


Route::get('/posisi',[PosisiController::class,'superadminindex'])->name('superadmin.posisi.index');
Route::get('/posisi/create',[PosisiController::class,'superadmincreate'])->name('superadmin.posisi.create');
Route::post('/posisi/post',[PosisiController::class,'superadminstore'])->name('superadmin.posisi.store');
Route::get('/posisi/show/{id}',[PosisiController::class,'superadminshow'])->name('superadminshowposisi');
Route::post('/posisi/update/{id}', [PosisiController::class,'superadminupdate'])->name('superadminupdateposisi');
Route::delete('/deleteposisi/{id}', [PosisiController::class, 'superadmindestroy'])->name('superadmindeleteposisi');


Route::get('/wilayah',[WilayahController::class,'superadminindex'])->name('superadmin.wilayah.index');
Route::get('/wilayah/create',[WilayahController::class,'superadmincreate'])->name('superadmin.wilayah.create');
Route::post('/wilayah/post',[WilayahController::class,'superadminstore'])->name('superadmin.wilayah.store');
Route::get('/wilayah/show/{id}',[WilayahController::class,'superadminshow'])->name('superadminshowwilayah');
Route::post('/wilayah/update/{id}', [WilayahController::class,'superadminupdate'])->name('superadminupdatewilayah');
Route::delete('/deletewilayah/{id}', [WilayahController::class, 'superadmindestroy'])->name('superadmindeletewilayah');

Route::get('/kandidat',[KandidatController::class,'superadminindex'])->name('superadmin.kandidat.index');
Route::get('/kandidat/create',[KandidatController::class,'superadmincreate'])->name('superadmin.kandidat.create');
Route::post('/kandidat/post',[KandidatController::class,'superadminstore'])->name('superadmin.kandidat.store');
Route::get('/kandidat/show/{id}',[KandidatController::class,'superadminshow'])->name('superadminshowkandidat');
Route::post('/kandidat/update/{id}', [KandidatController::class,'superadminupdate'])->name('superadminupdatekandidat');
Route::delete('/deletekandidat/{id}', [KandidatController::class, 'superadmindestroy'])->name('superadmindeletekandidat');
Route::get('/kandidat/get-wilayah-by-posisi', [KandidatController::class, 'getWilayahByPosisi'])->name('superadmin.kandidat.getWilayahByPosisi');
Route::get('kandidat/get-wilayah-by-id', [KandidatController::class, 'getWilayahById'])->name('superadmin.kandidat.getWilayahById');


Route::get('/superadmin/kandidat/process-no-hp', [KandidatController::class, 'processNoHp'])->name('superadmin.kandidat.processNoHp');
// Add this route to your routes file
Route::post('/superadmin/kandidat/updateStatus', [KandidatController::class, 'updateStatus'])->name('superadmin.kandidat.updateStatus');
Route::post('superadmin/kandidat/updateStatus', [KandidatController::class, 'updateStatus'])->name('superadmin.kandidat.updateStatus');

Route::get('/logactivity',[LogActivityController::class,'superadminindex'])->name('superadmin.logactivity.index');

Route::get('superadmin/changepassword', [PasswordController::class,'showChangePasswordFormSuperAdmin'])->name('superadmin.password');
Route::post('superadmin/changepassword', [PasswordController::class,'superadminchangePassword'])->name('superadmin-change-password');

Route::get('belumproses',[ProsesRekrutmenController::class,'belumprosesindex'])->name('superadmin.belumproses.index');
Route::post('/superadmin/belumproses/process', [ProsesRekrutmenController::class, 'process'])->name('superadmin.belumproses.process');

Route::get('psikotes',[ProsesRekrutmenController::class,'psikotesindex'])->name('superadmin.psikotes.index');
Route::post('/superadmin/psikotes/process', [ProsesRekrutmenController::class, 'psikotesprocess'])->name('superadmin.psikotes.process');

Route::get('itvhr',[ProsesRekrutmenController::class,'itvhrindex'])->name('superadmin.itvhr.index');
Route::post('/superadmin/itvhr/process', [ProsesRekrutmenController::class, 'itvhrprocess'])->name('superadmin.itvhr.process');


Route::get('training',[ProsesRekrutmenController::class,'trainingindex'])->name('superadmin.training.index');
Route::post('/superadmin/training/process', [ProsesRekrutmenController::class, 'trainingprocess'])->name('superadmin.training.process');


Route::get('itvuser',[ProsesRekrutmenController::class,'itvuserindex'])->name('superadmin.itvuser.index');
Route::post('/superadmin/itvuser/process', [ProsesRekrutmenController::class, 'itvuserprocess'])->name('superadmin.itvuser.process');


Route::get('training',[ProsesRekrutmenController::class,'trainingindex'])->name('superadmin.training.index');
Route::post('/superadmin/training/process', [ProsesRekrutmenController::class, 'trainingprocess'])->name('superadmin.training.process');


Route::get('tandem',[ProsesRekrutmenController::class,'tandemindex'])->name('superadmin.tandem.index');
Route::post('/superadmin/tandem/process', [ProsesRekrutmenController::class, 'tandemprocess'])->name('superadmin.tandem.process');

Route::get('lolos',[ProsesRekrutmenController::class,'lolosindex'])->name('superadmin.lolos.index');

Route::get('tidaklolos',[ProsesRekrutmenController::class,'tidaklolosindex'])->name('superadmin.tidaklolos.index');

Route::get('/detailtahapan/{id}',[KandidatController::class,'detailtahapan'])->name('detailtahapan');

Route::get('/sumber',[SumberController::class,'superadminindex'])->name('superadmin.sumber.index');
Route::get('/sumber/create',[SumberController::class,'superadmincreate'])->name('superadmin.sumber.create');
Route::post('/sumber/post',[SumberController::class,'superadminstore'])->name('superadmin.sumber.store');
Route::get('/sumber/show/{id}',[SumberController::class,'superadminshow'])->name('superadminshowsumber');
Route::post('/sumber/update/{id}', [SumberController::class,'superadminupdate'])->name('superadminupdatesumber');
Route::delete('/deletesumber/{id}', [SumberController::class, 'superadmindestroy'])->name('superadmindeletesumber');

Route::get('/masteraktif',[MasterAktifController::class,'superadminindex'])->name('superadmin.masteraktif.index');
Route::post('/importkaryawanaktif', [MasterAktifController::class,'import'])->name('importkaryawanaktif');
Route::get('/karyawanaktif/create',[MasterAktifController::class,'superadmincreate'])->name('superadmin.karyawanaktif.create');

Route::get('/mastertidakaktif',[MasterTidakAktifController::class,'superadminindex'])->name('superadmin.mastertidakaktif.index');
Route::post('/importkaryawantidakaktif', [MasterTidakAktifController::class,'import'])->name('importkaryawantidakaktif');

Route::get('/mastertrainingtandem',[MasterTrainingTandemController::class,'superadminindex'])->name('superadmin.mastertrainingtandem.index');
Route::post('/importkaryawantrainingtandem', [MasterTrainingTandemController::class,'import'])->name('importkaryawantrainingtandem');

Route::post('/superadmin/psikotes/mundurkan-status', action: [ProsesRekrutmenController::class, 'mundurkanStatus'])->name('superadmin.psikotes.mundurkanStatus');

Route::post('/superadmin/itvhr/mundurkan-status', action: [ProsesRekrutmenController::class, 'itvhrmundurkanStatus'])->name('superadmin.itvhr.mundurkanStatus');
Route::post('/superadmin/itvuser/mundurkan-status', action: [ProsesRekrutmenController::class, 'itvusermundurkanStatus'])->name('superadmin.itvuser.mundurkanStatus');
Route::post('/superadmin/training/mundurkan-status', action: [ProsesRekrutmenController::class, 'trainingmundurkanStatus'])->name('superadmin.training.mundurkanStatus');

Route::post('/superadmin/tandem/mundurkan-status', action: [ProsesRekrutmenController::class, 'tandemmundurkanStatus'])->name('superadmin.tandem.mundurkanStatus');


Route::post('/superadmin/lolos/mundurkan-status', action: [ProsesRekrutmenController::class, 'lolosmundurkanStatus'])->name('superadmin.lolos.mundurkanStatus');
Route::post('/superadmin/tidaklolos/mundurkan-status', action: [ProsesRekrutmenController::class, 'tidaklolosmundurkanStatus'])->name('superadmin.tidaklolos.mundurkanStatus');


Route::get('save',[ProsesRekrutmenController::class,'saveindex'])->name('superadmin.save.index');
Route::post('/superadmin/save/process', [ProsesRekrutmenController::class, 'saveprocess'])->name('superadmin.save.process');


Route::get('/masterkonfirm',[MasterKonfirmController::class,'superadminindex'])->name('superadmin.masterkonfirm.index');
Route::get('/masterkonfirm/create',[MasterKonfirmController::class,'superadmincreate'])->name('superadmin.masterkonfirm.create');
Route::post('/masterkonfirm/post',[MasterKonfirmController::class,'superadminstore'])->name('superadmin.masterkonfirm.store');
Route::delete('/deletemasterkonfirm/{id}', [MasterKonfirmController::class, 'superadmindestroy'])->name('superadmindeletemasterkonfirm');

Route::get('/getJumlahUndang', action: [MasterKonfirmController::class, 'getJumlahUndang'])->name('getJumlahUndang');

Route::get('/laporanperformance',[LaporanPerformaceController::class,'superadminindex'])->name('superadmin.laporanperformance.index');
Route::post('/laporanperformance/process',[LaporanPerformaceController::class,'store'])->name('laporanperformance.process');

Route::post('/laporanperformance/download', [LaporanPerformaceController::class, 'download'])->name('laporanperformance.download');
Route::delete('/deletelaporanperformance/{id}', [LaporanPerformaceController::class, 'superadmindestroy'])->name('delete.laporanperformance');

Route::delete('/deletekaryawanaktif/{id}', [MasterAktifController::class, 'superadmindestroy'])->name('deletekaryawanaktif');
Route::delete('/deletekaryawantidakaktif/{id}', [MasterTidakAktifController::class, 'superadmindestroy'])->name('deletekaryawantidakaktif');
Route::delete('/deletetrainingtandem/{id}', [MasterTrainingTandemController::class, 'superadmindestroy'])->name('deletetrainingtandem');

Route::get('/targetjumlah',[TargetJumlahController::class,'superadminindex'])->name('superadmin.targetjumlah.index');
Route::get('/targetjumlah/create',[TargetJumlahController::class,'superadmincreate'])->name('superadmin.targetjumlah.create');
Route::post('/targetjumlah/post',[TargetJumlahController::class,'superadminstore'])->name('superadmin.targetjumlah.store');

Route::get('/targetjumlah/show/{id}',[TargetJumlahController::class,'superadminshow'])->name('superadminshowtargetjumlah');
Route::post('/targetjumlah/update/{id}', [TargetJumlahController::class,'superadminupdate'])->name('superadminupdatetargetjumlah');
Route::delete('/deletetargetjumlah/{id}', [TargetJumlahController::class, 'superadmindestroy'])->name('superadmindeletetargetjumlah');

Route::post('/laporanperformance/export', [LaporanPerformaceController::class, 'export'])->name('laporanperformance.export');


Route::post('/importkandidat', [KandidatController::class,'import'])->name('importkandidat');
Route::get('/superadmin/penjadwalan', [ProsesRekrutmenController::class, 'jadwalindex'])->name('superadmin.penjadwalan');
Route::post('/superadmin/penjadwalan/store', [ProsesRekrutmenController::class, 'jadwalstore'])->name('superadmin.penjadwalan.store');
Route::get('/penjadwalan/show/{id}',[ProsesRekrutmenController::class,'showjadwal'])->name('showjadwal');

Route::post('/update-status', [ProsesRekrutmenController::class, 'updateStatus'])->name('update-status');
Route::post('/create-log-tahapan', [ProsesRekrutmenController::class, 'createLogTahapan'])->name('create-log-tahapan');


Route::get('/download-template', [KandidatController::class,'download'])->name('download.template');

Route::get('belumprosesafter',[ProsesRekrutmenController::class,'belumprosesafterindex'])->name('superadmin.belumprosesafter.index');


Route::get('psikotesesafter',[ProsesRekrutmenController::class,'psikotesesafterindex'])->name('superadmin.psikotesesafter.index');
Route::get('/superadmin/penjadwalanpsikotes', [ProsesRekrutmenController::class, 'jadwalindexpsikotes'])->name('superadmin.penjadwalanpsikotes');
Route::post('/superadmin/penjadwalanpsikotes/store', [ProsesRekrutmenController::class, 'jadwalstorepsikotes'])->name('superadmin.penjadwalanpsikotes.store');
Route::post('/create-log-tahapanpsikotes', [ProsesRekrutmenController::class, 'createLogTahapanpsikotes'])->name('create-log-tahapanpsikotes');



Route::get('itvhrafter',[ProsesRekrutmenController::class,'itvhrafterindex'])->name('superadmin.itvhrafter.index');
Route::get('/superadmin/penjadwalanitvhr', [ProsesRekrutmenController::class, 'jadwalindexitvhr'])->name('superadmin.penjadwalanitvhr');
Route::post('/superadmin/penjadwalanitvhr/store', [ProsesRekrutmenController::class, 'jadwalstoreitvhr'])->name('superadmin.penjadwalanitvhr.store');
Route::post('/create-log-tahapanitvhr', [ProsesRekrutmenController::class, 'createLogTahapanitvhr'])->name('create-log-tahapanitvhr');



Route::get('itvuserafter',[ProsesRekrutmenController::class,'itvuserafterindex'])->name('superadmin.itvuserafter.index');
Route::get('/superadmin/penjadwalanitvuser', [ProsesRekrutmenController::class, 'jadwalindexitvuser'])->name('superadmin.penjadwalanitvuser');
Route::post('/superadmin/penjadwalanitvuser/store', [ProsesRekrutmenController::class, 'jadwalstoreitvuser'])->name('superadmin.penjadwalanitvuser.store');
Route::post('/create-log-tahapanitvuser', [ProsesRekrutmenController::class, 'createLogTahapanitvuser'])->name('create-log-tahapanitvuser');

Route::get('itvuserdua',[ProsesRekrutmenController::class,'itvuserindexdua'])->name('superadmin.itvuserdua.index');
Route::get('itvuserafterdua',[ProsesRekrutmenController::class,'itvuserafterindexdua'])->name('superadmin.itvuserduaafter.index');
Route::get('/superadmin/penjadwalanitvuserdua', [ProsesRekrutmenController::class, 'jadwalindexitvuserdua'])->name('superadmin.penjadwalanitvuserdua');
Route::post('/superadmin/penjadwalanitvuserdua/store', [ProsesRekrutmenController::class, 'jadwalstoreitvuserdua'])->name('superadmin.penjadwalanitvuserdua.store');
Route::post('/create-log-tahapanitvuserdua', [ProsesRekrutmenController::class, 'createLogTahapanitvuserdua'])->name('create-log-tahapanitvuserdua');



Route::get('itvusertiga',[ProsesRekrutmenController::class,'itvuserindextiga'])->name('superadmin.itvusertiga.index');
Route::get('itvuseraftertiga',[ProsesRekrutmenController::class,'itvuserafterindextiga'])->name('superadmin.itvusertigaafter.index');
Route::get('/superadmin/penjadwalanitvusertiga', [ProsesRekrutmenController::class, 'jadwalindexitvusertiga'])->name('superadmin.penjadwalanitvusertiga');
Route::post('/superadmin/penjadwalanitvusertiga/store', [ProsesRekrutmenController::class, 'jadwalstoreitvusertiga'])->name('superadmin.penjadwalanitvusertiga.store');
Route::post('/create-log-tahapanitvusertiga', [ProsesRekrutmenController::class, 'createLogTahapanitvusertiga'])->name('create-log-tahapanitvusertiga');



Route::get('trainingafter',[ProsesRekrutmenController::class,'trainingafterindex'])->name('superadmin.trainingafter.index');
Route::get('/superadmin/penjadwalantraining', [ProsesRekrutmenController::class, 'jadwalindextraining'])->name('superadmin.penjadwalantraining');
Route::post('/superadmin/penjadwalantraining/store', [ProsesRekrutmenController::class, 'jadwalstoretraining'])->name('superadmin.penjadwalantraining.store');
Route::post('/create-log-tahapantraining', [ProsesRekrutmenController::class, 'createLogTahapantraining'])->name('create-log-tahapantraining');


Route::get('tandemafter',[ProsesRekrutmenController::class,'tandemafterindex'])->name('superadmin.tandemafter.index');
Route::get('/superadmin/penjadwalantandem', [ProsesRekrutmenController::class, 'jadwalindextandem'])->name('superadmin.penjadwalantandem');
Route::post('/superadmin/penjadwalantandem/store', [ProsesRekrutmenController::class, 'jadwalstoretandem'])->name('superadmin.penjadwalantandem.store');
Route::post('/create-log-tahapantandem', [ProsesRekrutmenController::class, 'createLogTahapantandem'])->name('create-log-tahapantandem');

Route::post('/update-statustandem', [ProsesRekrutmenController::class, 'updateStatustandem'])->name('update-statustandem');
Route::post('/update-statustraining', [ProsesRekrutmenController::class, 'updateStatustraining'])->name('update-statustraining');

Route::post('/update-jadwal', [ProsesRekrutmenController::class, 'updateLogTahapan'])->name('update-jadwal');
Route::post('/update-jadwalpsikotes', [ProsesRekrutmenController::class, 'updateLogTahapanpsikotes'])->name('update-jadwalpsikotes');
Route::post('/update-jadwalitvhr', [ProsesRekrutmenController::class, 'updateLogTahapanitvhr'])->name('update-jadwalitvhr');
Route::post('/update-jadwalitvuser', [ProsesRekrutmenController::class, 'updateLogTahapanitvuser'])->name('update-jadwalitvuser');
Route::post('/update-jadwaltraining', [ProsesRekrutmenController::class, 'updateLogTahapantraining'])->name('update-jadwaltraining');
Route::post('/update-jadwaltandem', [ProsesRekrutmenController::class, 'updateLogTahapantandem'])->name('update-jadwaltandem');

Route::get('saveafter',[ProsesRekrutmenController::class,'saveafterindex'])->name('superadmin.saveafter.index');
Route::get('/superadmin/penjadwalansave', [ProsesRekrutmenController::class, 'jadwalindexsave'])->name('superadmin.penjadwalansave');
Route::post('/superadmin/penjadwalansave/store', [ProsesRekrutmenController::class, 'jadwalstoresave'])->name('superadmin.penjadwalansave.store');
Route::post('/create-log-tahapansave', [ProsesRekrutmenController::class, 'createLogTahapansave'])->name('create-log-tahapansave');

Route::get('stopproses',[ProsesRekrutmenController::class,'stopprosesindex'])->name('superadmin.stopproses.index');

Route::get('/blacklist',[BlacklistController::class,'superadminindex'])->name('superadmin.blacklist.index');
Route::get('/blacklist/create',[BlacklistController::class,'superadmincreate'])->name('superadmin.blacklist.create');
Route::post('/blacklist/post',[BlacklistController::class,'superadminstore'])->name('superadmin.blacklist.store');

Route::delete('/deleteblacklist/{id}', [BlacklistController::class, 'superadmindestroy'])->name('superadmindeleteblacklist');


});


//REKRUTMEN 

Route::get('/rekrutmendashboard', [DashboardController::class,'rekrutmenindex'])->name('rekrutmendashboard');
Route::get('/trainerdashboard', [DashboardController::class,'trainerindex'])->name('trainerdashboard');

Route::get('/trainingtrainer', [ProsesRekrutmenController::class,'trainertrainerindex'])->name('trainingtrainer');
Route::get('/tandemtrainer', [ProsesRekrutmenController::class,'tandemtrainerindex'])->name('tandemtrainer');

