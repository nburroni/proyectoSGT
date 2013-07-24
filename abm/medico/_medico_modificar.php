<?php
// OK es una variable de flag que se activa cuando se guarda el formulario

$db = conectaDb();
$id = $_GET['id'];
$consulta = 'SELECT * from medico where (idmedico = "' . $id . '") ';
$result = $db->query($consulta);
$a = $result->fetch(PDO::FETCH_ASSOC);

// Consulta para conocer la especialidad del medico

$consulta = 'SELECT especialidad.nombre FROM medico INNER JOIN med_esp ON idmedico = id_med
INNER JOIN especialidad ON idespecialidad = id_esp WHERE idmedico = ' . $a['idmedico'] . '';
$conidesp = $db->query($consulta);
$arridesp = $conidesp->fetch(PDO::FETCH_ASSOC);
$esp = $arridesp['nombre'];
// Fin de la consulta

if (isset($_GET['ok'])) {
  $dni = $_GET['dni'];
  $nombre = $_GET['nombre'];
  $apellido = $_GET['apellido'];
  $mail = $_GET['mail'];
  $matricula = $_GET['matricula'];
  $tel = $_GET['tel'];
  $esp = $_GET['esp_selec'];
  $consulta = 'SELECT * from medico where (dni = "' . $dni . '") and idmedico != "' . $id . '" ';
  $result = $db->query($consulta);
  if (($result->rowCount() == 0)) {
    $consulta = 'UPDATE medico SET dni = "' . $dni . '", nombre="' . $nombre . '", apellido="' . $apellido . '", mail="' . $mail . '", telefono="' . $tel . '", matricula="' . $matricula . '" where idmedico = "' . $id . '"';
    $consulta2 = 'SELECT idespecialidad FROM especialidad WHERE nombre = "' . $esp . '" ';
    $re = $db->query($consulta2);
    $b = $re->fetch(PDO::FETCH_ASSOC);
    $up = 'UPDATE med_esp SET id_esp = ' . $b['idespecialidad'] . ' WHERE id_med =' . $id . '';
    if ($db->query($consulta) && ($db->query($up))) {
      $id = $db->lastInsertId("seq_name");
      $fechita = date('Y-m-d H:i:s');
      $detalle = 'Modificacion del médico  "' . $dni . '"';
      $user = $_SESSION['usuario']['user'];
      $log = "INSERT INTO log ( fecha, usuario, detalle, tabla, idafectado)              
              VALUES ('$fechita', '$user', '$detalle', 'medico', '$id' )";
      $db->query($log);
      echo '<div class="alert alert-success">  
                    <a class="close" data-dismiss="alert">×</a>  
                    <strong><h4>Muy Bien! Se modifico correctamente el medico: ' . $nombre . '</h4>.</strong>  
            </div>';
      //reconsulta para actualizar los valores del formulario al modificar algo
      $id = $_GET['id'];
      $consulta = 'SELECT * from medico where (idmedico = "' . $id . '") ';
      $result = $db->query($consulta);
      $a = $result->fetch(PDO::FETCH_ASSOC);

// Consulta para conocer la especialidad del medico

      $consulta = 'SELECT especialidad.nombre FROM medico INNER JOIN med_esp ON idmedico = id_med
INNER JOIN especialidad ON idespecialidad = id_esp WHERE idmedico = ' . $a['idmedico'] . '';
      $conidesp = $db->query($consulta);
      $arridesp = $conidesp->fetch(PDO::FETCH_ASSOC);
      $esp = $arridesp['nombre'];
// Fin de la consulta
    } else {
      echo '<div class="alert alert-error">  
                    <a class="close" data-dismiss="alert">×</a>  
                    <strong><h4>Ocurrio un error al conectarse con la base de datos.</h4>Por favor comuniquese con su administrador.</strong>  
                 </div>';
    }
  } else {
    echo '<div class="alert alert-error">  
                <a class="close" data-dismiss="alert">×</a>  
                <strong><h4>Error!</h4> Ya existe un medico con este DNI</strong>.  
             </div>';
  }
}
?>
<form class="form-horizontal" name="formi" action="./medico.php" method="GET">
  <fieldset>
    <legend>Modificación de médico</legend>
    <div class="control-group">
      <div class="controls">
        <label>Nuevo nombre</label>
        <input value="<?php echo $a['nombre'] ?>" type="text" class="input-xlarge" id="nombre" name="nombre" onkeypress="return soloLetras(event);">
        <label>Nuevo apellido</label>
        <input value="<?php echo $a['apellido'] ?>" type="text" class="input-xlarge" id="apellido" name="apellido" onkeypress="return soloLetras(event);">
        <label>Nuevo DNI</label>
        <input value="<?php echo $a['dni'] ?>" type="text" class="input-xlarge" id="dni" name="dni" maxlength="8" onkeypress="return justNumbers(event);">
        <label>Nueva matrícula</label>
        <input value="<?php echo $a['matricula'] ?>" type="text" class="input-xlarge" id="matricula" name="matricula" maxlength="8" onkeypress="return justNumbers(event);">
        <label>Nuevo mail</label>
        <input value="<?php echo $a['mail'] ?>" type="text" class="input-xlarge" id="mail" name="mail">
        <label>Nuevo teléfono</label>
        <input value="<?php echo $a['telefono'] ?>" type="text" class="input-xlarge" id="tel" name="tel" maxlength="10" onkeypress="return justNumbers(event);">
        <label>Nueva especialidad</label>
        <?php
        $datab = conectaDb();
        $consulta = "SELECT * FROM especialidad where activa = 1";
        $result = $datab->query($consulta);
        if (!$result)
          print ("<p>error en la consulta<p>");
        else
          
          ?>
        <select class="select-xlarge" id="esp_selec" name="esp_selec" >
          <?php
          foreach ($result as $valor)
            if ($valor['nombre'] == $esp) {
              echo '<option selected="selected">' . $valor['nombre'] . '</option>';
            } else {
              echo '<option>' . $valor['nombre'] . '</option>';
            }
          ?>
        </select>





        <!--comienzo del codigo para os y horarios modif-->


        <legend>Obras socialies del medico</legend>

        <fieldset>

          <div id="obrasSociales">

            <?php
            $r = 1;
            $consulos = "SELECT os.nombre nombre 
                          FROM os
                          INNER JOIN med_os ON ( id_os = idos ) 
                          INNER JOIN medico ON ( idmedico = id_med
                          AND idmedico =31 ) ";
            $pdoos = $db->query($consulos);
            if (!$pdoos)
              echo "algo malo paso";
            else
              foreach ($pdoos as $filaos):
                ?>
                <div id="os_div_<?php echo $r; ?>">
                  <legend><button onClick="borrarOs(<?php echo $r; ?>);
                return false;"><i class="icon-remove"></i></button>Obra social numero <?php echo $r ?></legend>
                  <fieldset>
                    <?php
                    $db = conectaDb();
                    $consulta = "SELECT * FROM os where activo = 1";
                    $result = $db->query($consulta);
                    if (!$result)
                      print ("<p>error en la consulta<p>");
                    else
                      
                      ?>
                    <select tabindex="12" class="select-xlarge" name="os_select[<?php echo $r; ?>][nombre]" >
                      <?php
                      $consulta = "SELECT * FROM os where activo = 1";
                      $result = $db->query($consulta);
                      if (!$result)
                        print ("<p>error en la consulta<p>");
                      foreach ($result as $valor):
                        if ($valor['nombre'] == $filaos['nombre']):
                          ?>

                          <option Selected="Selected"><?php echo $valor['nombre'] ?></option>

                        <?php else: ?>

                          <option><?php echo $valor['nombre'] ?></option>

                        <?php
                        endif;
                      endforeach;
                      ?>
                    </select>

                  </fieldset>
                  <br>
                </div>
              <?php 
              $r++;
               endforeach; ?>
          </div>

          <button class="btn btn-success" onClick="agregarObraSocial();
            return false;">Agregar Obra Social </button>

        </fieldset>

        <legend>Horarios del medico</legend>

        <fieldset>

          <div id="grillahoraria">
            <?php
            $i = 1;
            $consulhorarios = "SELECT dia, MIN(desde) desde, MAX(hasta) hasta, id_med
                              FROM horario
                              INNER JOIN medico ON ( id_med = '$id' )
                              group by id_med, dia ";
            $pdoHorario = $db->query($consulhorarios);
            foreach ($pdoHorario as $filaHorario):
              ?>

              <div id="horario_div_<?php echo $i; ?>">
                <legend><button onClick="borrarHorario(<?php echo $i; ?>);
              return false;"><i class="icon-remove"></i></button>Horario numero <?php echo $i; ?></legend>
                <fieldset>
                  <?php
                  $n = $i;
                  $semana = array("lun", "mar", "mie", "jue", "vie");
                  ?>
                  <label>Día</label>
                  <select class="span5" name="horario[<?php echo $i; ?>][dia]" id="dia<?php echo $n; ?>" >
                    <?php foreach ($semana as $k): ?>
                      <?php if ($k == $filaHorario["dia"]): ?>
                        <option value="<?php echo $k ?>" selected="selected"><?php echo $k ?></option>
                      <?php else: ?>
                        <option value="<?php echo $k ?>" ><?php echo $k ?></option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </select>

                  <label>Desde</label>
                  <select class="span5" name="horario[<?php echo $i; ?>][desde]" id="horarios<?php echo $n; ?>">
                    <?php for ($p = 8; $p < 20; $p++): ?>
                      <?php if ($p . ':00:00' == $filaHorario['desde']): ?>
                        <option value="<?php echo $p ?>:00:00" selected="selected"><?php echo $p ?>:00</option>
                      <?php else: ?>
                        <option value="<?php echo $p ?>:00:00" ><?php echo $p ?>:00</option>
                      <?php endif; ?>                        
                    <?php endfor; ?>
                  </select>

                  <label>Hasta</label>
                  <select class="span5" name="horario[<?php echo $i; ?>][hasta]" id="horariosHasta<?php echo $n; ?>" onchange="verificarHorario(numeroHorario - 1)">
                    <?php for ($p = 8; $p < 20; $p++): ?>
                      <?php if ($p . ':00:00' == $filaHorario['hasta']): ?>
                        <option value="<?php echo $p ?>:00:00" selected="selected"><?php echo $p ?>:00</option>
                      <?php else: ?>
                        <option value="<?php echo $p ?>:00:00" ><?php echo $p ?>:00</option>
                      <?php endif; ?>                        
                    <?php endfor; ?>
                  </select>

                </fieldset>
                <br>
              </div>
              <?php
              $i++;
            endforeach;
            ?>
          </div>

          <button class="btn btn-success" onClick="agregarHorarioMedico();


            return false;">Agregar Horario </button>

          <input type="submit" class="btn btn-success"  onClick="return veriformuMed();" value="Guardar Medico ">

        </fieldset>


        <div class="form-actions">
          <input type="hidden" name="code" value="m"/>
          <input type="hidden" name="ok" value="1"/>
          <input type="hidden" name="id" value="<?php echo "$id" ?>"/>
          <button type="submit"  class="btn btn-success">Guardar cambios</button> <!-- Agregarle la funcion de validacion -->
          <button type="reset" class="btn btn-success">Reiniciar</button>

        </div>
      </div>
    </div>
  </fieldset>
</form>
<script>
                      var numeroHorario = <?php echo $i?>;
                    var numeroOs = <?php echo $r?>;

                    function agregarHorarioMedico()
                    {
                        $.ajax({
                            url: 'medico/horario.php',
                            type: 'POST',
                            data: {
                                numeroHorario: numeroHorario++
                            },
                            success: function(data) {
                              console.warn(numeroHorario);
                                $('#grillahoraria').append(data);
                            }
                        });
                    }

                    function agregarObraSocial()
                    {
                        $.ajax({
                            url: 'medico/addOs.php',
                            type: 'POST',
                            data: {
                                numeroOs: numeroOs++
                            },
                            success: function(data) {
                                $('#obrasSociales').append(data);
                            }
                        });
                    }
                    function borrarOs(numeroDiv)
                    {
                        $('#os_div_' + numeroDiv).remove();
                          var hidden = '<input type="hidden" name="horario'+numeroDiv+'" value="'+numeroDiv+'"/>';
                        $('#obrasSociales').append(hidden);
                    }
                    function borrarHorario(numeroDiv)
                    {
                        $('#horario_div_' + numeroDiv).remove();
                        var hidden = '<input type="hidden" name="horario'+numeroDiv+'" value="'+numeroDiv+'"/>';
                        $('#grillahoraria').append(hidden);
                    }

</script>