<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="<?=APP_ROOT?>css/style.css" rel="stylesheet" type="text/css" /> 
    <title><?php echo $tituloPagina; ?></title>
    <script src="<?=APP_ROOT?>js/config.js"></script>
</head>
<body>

    <?php require APP_PATH . "html_parts/info_usuario.php" ?>

    <div class="header">
        <h1>Práctica 05</h1>
        <h2>Basic Server Side Programming</h2>
    </div>
      
    <?php require APP_PATH . "html_parts/menu.php"; ?>
      
    <div class="row">

        <div class="leftcolumn">

            <div class="card">
                <h2>AJAX GET Request</h2>
                <p>
                    La petición que hacemos se realiza con AJAX GET, y nos regresa un JSON. 
                    La respuesta JSON la podemos usar comodamente en Javascript porque
                    un JSON lo transformamos en un object JS nativo.
                </p>
                <p>Fecha hora del server: <strong id="lbl-fecha-hora">[NO OBTENIDO]</strong></p>
                <p>
                    <input id="btn-obtener-fecha-hora" type="button" value="Obtener Fecha Hora"> 
                    <input id="btn-obtener-fecha-hora-async" type="button" value="Obtener Fecha Hora Async">
                </p>                
            </div>

            <div class="card">
                <h2>AJAX POST Request</h2>
                <p>
                    Se envían los datos usando una petición AJAX POST. Los datos se envian 
                    dentro de la petición, esta petición es de tipo multipart/form-data, 
                    puesto que es el default del objeto FormData que usamos para poner 
                    los datos dentro de la petición.
                </p>
                <form id="form-post-ajax" action="ajax/post-example.php" method="POST">
                    <table>
                        <tr>
                            <td><label for="txt-nombre">Nombre: </label></td>
                            <td><input type="text" name="nombre" id="txt-nombre" required /></td>
                        </tr>
                        <tr>
                            <td><label for="txt-apellidos">Apellidos: </label></td>
                            <td><input type="text" name="apellidos" id="txt-apellidos" required /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="Enviar" style="width: 100%;"></td>
                        </tr>
                    </table>
                </form>
            </div>

            <div class="card">
                <h2>AJAX POST Request enviando un JSON</h2>
                <p>
                    Aquí los datos se van a enviar en la serialización JSON (que es un string),
                    que posteriomente lo recibiremos del lado del server y ese JSON lo vamos a 
                    transformar en un assoc array para poder obtener los datos que se enviaron.
                </p>
                <form id="form-post-ajax-json" action="ajax/recibe-datos-json.php" method="POST">
                    <label for="txt-nombre-json">Nombre: </label>
                    <input type="text" name="nombre" id="txt-nombre-json" required />
                    <br />
                    <label for="txt-apellidos-json">Apellidos: </label>
                    <input type="text" name="apellidos" id="txt-apellidos-json" required />
                    <br />
                    <input type="submit" value="Enviar">
                </form>
            </div>

            <div class="card">
                <h2>Enviar Archivos con AJAX</h2>
                <table>
                    <tbody>
                        <tr> 
                            <td><label for="txt-otro-dato">Otro Dato: </label></td>
                            <td><input type="text" name="otroDato" id="txt-otro-dato" /></td>
                        </tr>
                        <tr>
                            <td><label for="input-archivo">Archivo: </label></td>
                            <td><input type="file" name="archivo" id="input-archivo" accept=".jpg,.jpeg,.png,.gif" required /></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input id="btn-enviar-archivo" type="submit" value="Guardar Archivo" /></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="file-list">

    <h2>Mis Archivos</h2>
    <label for="mes">Mes:</label>
    <select id="mes">
        <option value="1">Enero</option>
        <!-- Resto de los meses -->
    </select>
    <label for="anio">Año:</label>
    <input type="number" id="anio" value="2024" />

    <button id="filtrar">Filtrar</button>

    <table id="archivos-table">
        <thead>
            <tr>
                <th>Archivo</th>
                <th>Fecha Subida</th>
                <th>Peso (KB)</th>
                <th>Descargas</th>
                <th>Público</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <script>
document.getElementById('filtrar').addEventListener('click', () => {
    const mes = document.getElementById('mes').value;
    const anio = document.getElementById('anio').value;

    fetch(`${APP_ROOT}ajax/get_archivos.php?mes=${mes}&anio=${anio}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#archivos-table tbody');
            tbody.innerHTML = '';

            data.forEach(archivo => {
                const row = `
                    <tr>
                        <td><a href="archivo.php?id=${archivo.id}" target="_blank">${archivo.nombre_original}</a></td>
                        <td>${archivo.fecha_hora_subida}</td>
                        <td>${archivo.peso_kb}</td>
                        <td>${archivo.cant_descargas}</td>
                        <td>
                            <input type="checkbox" class="publico" data-id="${archivo.id}" ${archivo.es_publico ? 'checked' : ''} />
                        </td>
                        <td>
                            <button class="eliminar" data-id="${archivo.id}">Eliminar</button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });

            document.querySelectorAll('.publico').forEach(checkbox => {
                checkbox.addEventListener('change', togglePublico);
            });

            document.querySelectorAll('.eliminar').forEach(btn => {
                btn.addEventListener('click', eliminarArchivo);
            });
        });
});

function togglePublico(e) {
    const id = e.target.dataset.id;
    const publico = e.target.checked ? 1 : 0;

    fetch(`${APP_ROOT}ajax/toggle_publico.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, publico })
    }).then(res => res.json())
      .then(data => alert(data.mensaje));
}

function eliminarArchivo(e) {
    const id = e.target.dataset.id;

    if (!confirm("¿Deseas eliminar este archivo?")) return;

    fetch(`${APP_ROOT}ajax/eliminar_archivo.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
    }).then(res => res.json())
      .then(data => {
          alert(data.mensaje);
          if (!data.error) e.target.closest('tr').remove();
      });
}
</script>
<td><a href="<?=APP_ROOT?>ajax/descargar_archivo.php?id=${archivo.id}" target="_blank">${archivo.nombre_original}</a></td>

        </tbody>
    </table>
</div>

        </div>  <!-- End left column -->

        <!-- Incluimos la parte derecha de la página, que está procesada en otro archivo -->
        <?php require APP_PATH . "html_parts/page_right_column.php"; ?>

    </div>  <!-- End row-->

    <div class="footer">
        <h2>ITI - Programación Web</h2>
    </div>

    <script src="<?=APP_ROOT?>js/enviar_datos_con_ajax.js"></script>
    
    

</body>
</html>
