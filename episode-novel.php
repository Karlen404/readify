<?php

require 'helpers/auth.php';

redirectIfNotAuthenticated('login.php');

$novelSlug = $_GET['novel_slug'];
$episodeSlug = $_GET['episode_slug'];

$episodeSql = 'SELECT episode_novel.* FROM episode_novel JOIN novel ON episode_novel.id_novel = novel.id WHERE novel.slug = :novel_slug AND episode_novel.slug = :episode_slug';
$episodeParams = [':novel_slug' => $novelSlug, ':episode_slug' => $episodeSlug];
$episode = fetchOne($episodeSql, $episodeParams);

if (!$episode) {
  redirect('404.html');
}

// hapus novel
if (isset($_POST['hapus'])) {
  $episodeId = $_POST['id_episode'];

  beginTransaction();

  try {
    $deleteEpisodeSql = 'DELETE FROM episode_novel WHERE id = :id';
    $deleteEpisodeParams = [':id' => $episodeId];
    query($deleteEpisodeSql, $deleteEpisodeParams);

    commit();

    setAlert('success', 'Episode berhasil dihapus');
    redirect('detail-novel-saya.php?slug='.$novelSlug);
  } catch (PDOException $error) {
    rollBack();
    setAlert('danger', 'Gagal menghapus episode');
  }
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= $episode['judul']; ?></title>

    <?php require 'layouts/favicon.php'; ?>
    <?php require 'layouts/styles.php'; ?>
  </head>

  <body>
    <main>
      <?php require 'layouts/navbar.php'; ?>
    
      <header class="site-header site-header-no-title d-flex flex-column justify-content-center align-items-center">
      </header>

      <section class="latest-podcast-section section-padding pb-0" id="section_2">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-12">
              <?= getAlert(); ?>
            </div>
            <div class="col-12 d-flex justify-content-between mb-3">
              <a href="detail-novel-saya.php?slug=<?= $novelSlug; ?>" class="btn custom-btn">
                <i class="bi-arrow-left"></i>
                Kembali
              </a>

              <div>
                <button id="hapus" class="btn custom-btn">
                  <i class="bi-trash"></i>
                  Hapus
                </button>
                <a href="edit-episode.php?novel_slug=<?= $novelSlug ?>&episode_slug=<?= $episodeSlug; ?>" class="btn custom-btn">
                  <i class="bi-pencil"></i>
                  Edit
                </a>
              </div>
            </div>
            <div class="col-12">
              <div class="custom-block-info">
                <!-- Judul -->
                <h2 class="text-center mb-5"><?= $episode['judul']; ?></h2>

                <!-- Konten -->
                <div class="mb-5 content-episode">
                  <?= $episode['konten']; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <form action="episode-novel.php?novel_slug=<?= $novelSlug ?>&episode_slug=<?= $episodeSlug; ?>" id="submit-hapus" method="post" hidden>
      <input type="hidden" name="hapus" />
      <input type="hidden" name="id_episode" value="<?= $episode['id']; ?>" />
    </form>

    <?php require 'layouts/footer.php'; ?>
    <?php require 'layouts/scripts.php'; ?>

    <script src="js/sweetalert2.all.min.js"></script>

    <script>
      $('#hapus').click(function () {
        Swal.fire({
          title: '<h6>Apakah anda yakin ingin menghapus episode ini?</h6>',
          showCancelButton: true,
          cancelButtonText: 'Batal',
          confirmButtonText: 'Hapus',
          confirmButtonColor: 'red',
        }).then((result) => {
          if (result.isConfirmed) {
            $("form#submit-hapus").submit();
          }
        })
      })
    </script>
  </body>
</html>