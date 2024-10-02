<?php 
  session_start();
  if(!isset($_SESSION['UserName']) || !isset($_SESSION['CliId'])){
    header("location:/gesman");
    exit();
  }
  require_once $_SERVER['DOCUMENT_ROOT']."/gesman/connection/ConnGesmanDb.php";
  require_once $_SERVER['DOCUMENT_ROOT']."/checklist/datos/checklistData.php";
  
  $CLIID = $_SESSION['CliId'];
  $ID = empty($_GET['id'])?0:$_GET['id']; 
  $tablaActividades = array();
  $observaciones = array();
  $isAuthorized = false;
  $claseHabilitado = "btn-outline-secondary";
  $atributoHabilitado = " disabled";
  $NUMERO=0;
  try {
    $conmy->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $checklist = FnBuscarChecklist($conmy, $ID, $CLIID);
    if(!empty($checklist->Id)){
      $isAuthorized = true;
      $claseHabilitado = "btn-outline-primary";
      $atributoHabilitado = "";      
      $tablaActividades = FnBuscarTablaActividades($conmy, $ID);
      $observaciones = FnBuscarObservaciones($conmy, $ID);
    }
  } catch (PDOException $e) {
      $errorMessage = $e->getMessage();
      $conmy = null;
  } catch (Exception $e) {
      $errorMessage = $e->getMessage();
      $conmy = null;
  }

?>
<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checklist | GPEM SAC</title>
    <link rel="shortcut icon" href="/mycloud/logos/favicon.ico">
    <link rel="stylesheet" href="/mycloud/library/fontawesome-free-5.9.0-web/css/all.css">
    <link rel="stylesheet" href="/mycloud/library/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/mycloud/library/select-gpem-1.0/css/select-gpem-1.0.css">
    <link rel="stylesheet" href="/mycloud/library/gpemsac/css/gpemsac.css"> 
    <link rel="stylesheet" href="/gesman/menu/sidebar.css">
    <style>
      .hijos p:first-child{ padding-top: 10px;}
      .contenedor-imagen{display:grid;grid-template-columns:1fr 1fr !important;gap:10px;}
      @media(min-width:768px){.contenedor-imagen{grid-template-columns:1fr 1fr 1fr 1fr !important;}}
      @media(max-width:768px){.contenedor-radio{padding-left:30px;}}
      /* .img-fluid{height:100%;} */
      .imagen-observacion{display:grid; display:grid;grid-template-columns:25% 50% 25%; }
      @media(min-width:768px){.imagen-observacion{grid-template-columns:2fr 1.5fr 2fr}}
    </style>
  </head>
  <body>

    <?php require_once $_SERVER['DOCUMENT_ROOT'].'/gesman/menu/sidebar.php';?>

    <div class="container section-top">
      <div class="row mb-3">
        <div class="col-12 btn-group" role="group" aria-label="Basic example">
          <button type="button" class="btn btn-outline-primary fw-bold" onclick="FnListarChecklists(); return false;"><i class="fas fa-list"></i><span class="d-none d-sm-block"> Checklists</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnEditarChecklist(<?php echo $ID ?>); return false;"><i class="fas fa-edit"></i><span class="d-none d-sm-block"> Editar</span></button>
          <!-- <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnModalFinalizarChecklist(); return false;"><i class="fas fa-check-square"></i><span class="d-none d-sm-block"> Finalizar</span></button>
          <button type="button" class="btn btn-outline-primary fw-bold <?php echo $claseHabilitado;?> <?php echo $atributoHabilitado;?>" onclick="FnDescargarChecklist(); return false;"><i class="fas fa-download"></i><span class="d-none d-sm-block"> Descargar</span></button> -->
        </div>
      </div>
  
      <div class="row border-bottom mb-2 fs-5">
        <div class="col-12 fw-bold d-flex justify-content-between">
          <p class="m-0 text-secondary"><?php echo $isAuthorized ? $_SESSION['CliNombre'] : 'UNKNOWN'; ?></p>
          <input type="hidden" id="txtIdPlantilla" value="0"/>
          <p class="m-0 text-secondary"><?php echo $isAuthorized ? $checklist->Nombre : 'UNKNOWN'; ?></p>
        </div>
      </div>

      <?php $NUMERO+=1; ?>
      
      <?php if ($isAuthorized): ?>
        <!-- DATOS GENERALES -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 m-0 border-bottom bg-light" >
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?> - DATOS GENERALES</p>
          </div>
          <div class="row p-1 m-0">
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Fecha</p> 
              <p class="m-0"><?php echo $checklist->Fecha ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Cliente:</p> 
              <p class="m-0"><?php echo $checklist->CliNombre ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Teléfono:</p> 
              <p class="m-0"><?php echo $checklist->CliTelefono ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Correo:</p> 
              <p class="m-0"><?php echo $checklist->CliCorreo ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 15px;">Supervisor</p> 
              <p class="m-0"><?php echo $checklist->Supervisor ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary" style="font-size: 12px;">Estado</p>
              <?php
                switch ($checklist->Estado){
                  case 1:
                    echo "<span class='badge bg-danger'>Anulado</span>";
                    break;
                  case 2:
                    echo "<span class='badge bg-primary'>Abierto</span>";
                    break;
                  case 3:
                    echo "<span class='badge bg-success'>Cerrado</span>";
                    break;
                  default:
                    echo "<span class='badge bg-secondary'>Unknown</span>";
                }
              ?>
            </div>
          </div>
        </div>
        
        <?php $NUMERO+=1; ?>
        <!-- DATOS DEL EQUIPO -->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-0 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?> - DATOS DEL EQUIPO</p>
          </div>
          <div class="row p-1 m-0">
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary fw-light" style="font-size: 15px;">Nombre Equipo</p>
              <p class="m-0"><?php echo $checklist->EquNombre ?></p>              
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary fw-light" style="font-size: 15px;">Modelo Equipo</p> 
              <p class="m-0"><?php echo $checklist->EquModelo ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary fw-light" style="font-size: 15px;">Serie Equipo</p> 
              <p class="m-0"><?php echo $checklist->EquSerie ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary fw-light" style="font-size: 15px;">Marca Equipo</p> 
              <p class="m-0"><?php echo $checklist->EquMarca ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary fw-light" style="font-size: 15px;">Kilometraje</p> 
              <p class="m-0"><?php echo $checklist->EquKm ?></p>
            </div>
            <div class="col-6 col-sm-4 col-lg-4 mb-1">
              <p class="m-0 text-secondary fw-light" style="font-size: 15px;">Horas Motor</p> 
              <p class="m-0"><?php echo $checklist->EquHm ?></p>
            </div>
          </div>
        </div>
        
        <?php $NUMERO+=1; ?>
        <!-- CHECKLIST-->
        <div class="row p-1 mb-2 mt-2">
          <div class="col-12 mb-2 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?> - CHECKLIST</p>
          </div>
          <div class="contenedor-imagen mt-2">
            <div class="card p-0 h-100">
              <div class="card-header p-0 bg-transparent text-center">Lado derecho</div>
              <img src="/mycloud/gesman/files/<?php echo empty($checklist->Imagen1) ? '0.jpg' : $checklist->Imagen1 ?>" class="img-fluid" alt="">
              <div class="card-footer p-0 text-center"></div>
            </div>
            <div class="card p-0 h-100">
              <div class="card-header p-0 bg-transparent text-center">Anterior</div>
              <img src="/mycloud/gesman/files/<?php echo empty($checklist->Imagen2) ? '0.jpg' : $checklist->Imagen2 ?>" class="img-fluid" alt="">
              <div class="card-footer p-0 text-center"></div>
            </div>
            <div class="card p-0 h-100">
              <div class="card-header p-0 bg-transparent text-center">Lado izquierdo</div>
              <img src="/mycloud/gesman/files/<?php echo empty($checklist->Imagen3) ? '0.jpg' : $checklist->Imagen3 ?>" class="img-fluid" alt="">
              <div class="card-footer p-0 text-center"></div>
            </div>
            <div class="card p-0 h-100">
              <div class="card-header p-0 bg-transparent text-center">Posterior</div>
              <img src="/mycloud/gesman/files/<?php echo empty($checklist->Imagen4) ? '0.jpg' : $checklist->Imagen4 ?>" class="img-fluid" alt="">
              <div class="card-footer p-0 text-center"></div>
            </div>
          </div>
        </div>

        <div class="row mb-2 mt-3">
          <?php 
            $html = '';
            if (is_array($tablaActividades) && !empty($tablaActividades)) {
              foreach($tablaActividades as $actividad) {
                $html .= '
                <div class="border border-1 mb-2 pt-2 pb-2">
                <div class="row">
                  <div class="col-10">
                    <div class="d-flex justify-content-start align-items-center">
                      <i class="far fa-check-square text-secondary" style="margin-right:10px;"></i>
                      <p class="mb-0 text-secondary" id="idActividad" style="text-align: justify;">'.$actividad['descripcion'].'</p>
                    </div>
                  </div>
                  <div class="col-2">
                    <div class="d-flex justify-content-end contenedor-radio" style="gap:10px" id="contenedorAlternativas">
                      <span>'.$actividad['respuesta'].'</span>
                    </div>
                  </div>';
                // MOSTRAR OBSERVACION SI EXISTE
                if (!empty($actividad['observaciones'])) {
                  $html .= '
                  <div class="col-12 mb-2 mt-2 d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                      <label class="text-secondary fw-bold" style="margin-left:24px;">Observación : </label>
                      <p class="mb-0 text-secondary observacion" id="idActividad" style="text-align: justify; margin-left:10px;">'.$actividad['observaciones'].'</p>
                    </div>
                  </div>';
                }

                // MOSTRAR ARCHIVO SI EXISTE
                if (!empty($actividad['archivo'])) {
                  $html .= '
                  <div class="col-12 p-1 mb-1 imagen-observacion">
                    <div class="card p-0 archivo" style="grid-column:2/3">
                      <div class="card-header p-0 bg-transparent text-center">Imagen</div>
                      <img src="/mycloud/gesman/files/'.$actividad['archivo'].'" class="img-fluid" alt="">
                      <div class="card-footer p-0 text-center">
                          <button class="bg-light text-secondary w-100 text-center border border-0" onclick="FnEliminarArchivo()">ELIMINAR</button>
                      </div>
                    </div>
                  </div>';
                }
                $html .= 
                '</div></div>'; 
              }
            }
            echo $html;
          ?>
        </div>

        <?php $NUMERO+=1; ?>
        <!-- OBSERVACIONES -->
        <div class="row mb-2 mt-2">
          <div class="col-12 mb-2 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"><?php echo $NUMERO; ?> - OBSERVACIONES</p>
          </div>
          <?php 
          $html = '';
          foreach($observaciones as $observacion) {
              $html .= '
              <div class="row mb-2">
                <div class="col-12">
                  <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex">
                      <i class="fas fa-check text-secondary" style="margin-right:10px; margin-top:8px; font-size:10px;"></i>
                      <p class="mb-0 text-secondary" id="idObservacion" style="text-align: justify;">'.$observacion['descripcion'].'</p>
                    </div>
                  </div>
                </div>';
                if ($observacion['archivo']) {
                  $html .= '
                  <div class="p-1 mb-1 mt-2 imagen-observacion">
                    <div class="card p-0" style="grid-column:2/3">
                      <div class="card-header bg-transparent text-center">Imagen</div>
                      <img src="/mycloud/gesman/files/'.$observacion['archivo'].'" class="img-fluid" alt="">
                    </div>                    
                  </div>';
                }
              $html .= '
              </div>';
          }  
          echo $html;
          ?>
        </div>

        <div class="row mb-2 mt-2">
          <div class="col-12 mb-2 border-bottom bg-light">
            <p class="mt-2 mb-2 fw-bold text-secondary"> V° B°</p>
          </div>
          <div class="col-6">
            <?php if(!empty($checklist->EmpFirma) || !empty($checklist->Supervisor)) : ?>
              <p class="text-center mb-0">Firma de supervisor</p>
              <div id="signatureCanvasSupervisor" class="d-flex justify-content-center align-items-center">
                <img src="/mycloud/gesman/files/<?php echo $checklist->EmpFirma ?>" class="img-fluid" alt="">
              </div>
              <p class="text-center mb-0"><?php echo $checklist->Supervisor ?></p>
            <?php endif ?>
          </div>
          <div class="col-6">
            <?php if(!empty($checklist->CliFirma) || !empty($checklist->CliContacto)) : ?>
              <p class="text-center mb-0">Firma de cliente</p>
              <div id="signatureCanvasAprobo" class="d-flex justify-content-center align-items-center">
                <img src="/mycloud/gesman/files/<?php echo $checklist->CliFirma ?>" class="img-fluid" alt="">
              </div>
              <p class="text-center mb-0"><?php echo $checklist->CliContacto ?></p>
            <?php endif ?>
          </div>
        </div>

        <!-- MODAL PARA FINALIZAR CHEKCLIST -->
        <div class="modal fade" id="modalFinalizarChecklist" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Finalizar Checklist</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>                
              <div class="modal-body pb-1">
                <div class="row text-center fw-bold pt-3">                        
                  <p class="text-center">Para finalizar el Checklist 001 haga clic en el botón CONFIRMAR.</p>                    
                </div>
              </div>
              <div class="modal-body pt-1" id="msjFinalizarChecklist"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="FnFinalizarChecklist(); return false;">CONFIRMAR</button>
              </div>              
            </div>
          </div>
        </div>
      <?php endif ?>
    </div>
    
    <script src="/checklist/js/Checklist.js"></script>
    <script src="/mycloud/library/bootstrap-5.0.2-dist/js/bootstrap.min.js"></script>
    <script src="/mycloud/library/bootstrap-5-alerta-1.0/js/bootstrap-5-alerta-1.0.js"></script>
    <script src="/gesman/menu/sidebar.js"></script>
  </body>
</html>