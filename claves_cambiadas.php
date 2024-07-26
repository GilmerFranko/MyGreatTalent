<?php
require("core.php");

if ($rowu['role']!='Admin') {
  echo '<meta http-equiv="refresh" content="0;url='.$sitio['site'].'messages.php">';
  exit;
}
head();

$total_pages = $connect->query("SELECT * FROM `player_changed_password` ORDER BY id DESC")->num_rows;
?>

<div class="content-wrapper">
  <div id="content-container">
    <div class="row" style="margin:0;">
      <section class="content">
        <div class="row">
          <div style="width: 100%;">
            <div class="box">
              <div class="box-body row-list">
                <?php

                $page = (isset($_GET['page']) && is_numeric($_GET['page']))? $_GET['page'] : 1;

                $num_results_on_page = 40;

                $calc_page = ($page - 1) * $num_results_on_page;

                $querycp = mysqli_query($connect, "SELECT *, p.id as p_id, pcp.id as pcp_id FROM player_changed_password as pcp INNER JOIN players as p ON p.id = pcp.player_id ORDER BY pcp.id DESC LIMIT {$calc_page}, {$num_results_on_page}");


                $countcp = mysqli_num_rows($querycp);
                ?>
                <div id="scroll">
                  <table class="table table-striped table-bordered table-hover" id="players">
                    <thead>
                      <tr>
                        <th style="text-align:center;">#</th>
                        <th style="text-align:center;">
                          <i class="fa fa-user"></i> Usuario
                        </th>
                        <th style="text-align:center;">
                          <i class="fa fa-database"></i> Fecha
                        </th>
                    </thead>
                    <?php
                    if ($countcp > 0) {
                      while ($rowcp = mysqli_fetch_assoc($querycp)) { ?>
                        <tr>
                          <th style="text-align:center;">
                            <span><?php echo $rowcp['pcp_id'] ?></span>
                          </th>
                          <th style="text-align:center;">
                            <a href="profile.php?profile_id=<?php echo $rowcp['p_id']; ?>"> <?php echo $rowcp['username'] ?> </a>
                          </th>
                          <th style="text-align:center;">
                            <span>
                              <?php echo date('Y-m-d H:i:s',$rowcp['date_changed']); ?>
                            </span>
                          </th>
                          </tr>
                        <?php } ?>
                      </table>
                    </div>
                    <?php if (ceil($total_pages / $num_results_on_page) > 0): ?>
                      <ul class="pagination">
                        <?php if ($page > 1): ?>
                          <li class="prev"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page-1)), true) ?>">Anterior</a></li>
                        <?php endif; ?>

                        <?php if ($page > 3): ?>
                          <li class="start"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => '1')), true) ?>">1</a></li>
                          <li class="dots">...</li>
                        <?php endif; ?>

                        <?php if ($page-2 > 0): ?><li class="page"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page-2)), true) ?>"><?php echo $page-2 ?></a></li><?php endif; ?>
                        <?php if ($page-1 > 0): ?><li class="page"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page-1)), true) ?>"><?php echo $page-1 ?></a></li><?php endif; ?>

                        <li class="currentpage"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page)), true) ?>"><?php echo $page ?></a></li>

                        <?php if ($page+1 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page+1)), true) ?>"><?php echo $page+1 ?></a></li><?php endif; ?>
                        <?php if ($page+2 < ceil($total_pages / $num_results_on_page)+1): ?><li class="page"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page+2)), true) ?>"><?php echo $page+2 ?></a></li><?php endif; ?>

                        <?php if ($page < ceil($total_pages / $num_results_on_page)-2): ?>
                          <li class="dots">...</li>
                          <li class="end"><a href="<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => ceil($total_pages / $num_results_on_page))), true) ?>"><?php echo ceil($total_pages / $num_results_on_page) ?></a></li>
                        <?php endif; ?>

                        <?php if ($page < ceil($total_pages / $num_results_on_page)): ?>
                          <li class="next"><a href='<?php echo createLink('claves_cambiadas', '', array_merge(array('page' => $page+1)), true) ?>'>Siguiente</a></li>
                        <?php endif; ?>
                      </ul>
                    <?php endif; ?>
                    <?php
                  } else {
                    echo '<div class="alert alert-info"><strong><i class="fa fa-info-circle"></i> Actualmente no hay Movimientos</strong></div>';
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
  <!-- JavaScript -->
  <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.11.1/build/alertify.min.js"></script>
  ?>
