<?php
    // Inicializa la sesión
    session_start();
    
    // Comprueba si el usuario esta logueado
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
    {
        header("location: login.php");
        exit;
    }

    $user_actual = $_SESSION['id']; // ID usuario cliente
    $user_otro = $_GET['id'];   // ID usuario con el que se chatea

    $link = mysqli_connect("localhost", "root", "", "pinf");

    // Comprobar si el usuario del ID introducido es amigo del cliente
    $comprobar_amistad = "SELECT * FROM amistades WHERE usuario1 = '$user_actual' AND usuario2 = '$user_otro' AND amigos = 1";

    if (mysqli_num_rows(mysqli_query($link, $comprobar_amistad)) == 0)    // Si no son amigos se devuelve al main
    {
        mysqli_close($link);
        header("location: main.php");
    }
    else
    {
?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>5&Bet - Chat</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
            <script src = "js/jquery-3.5.1.js"></script>

            <script type = "text/javascript">

                $(document).ready(function() {

                    setInterval (cargar_log, 500);  // Recarga el registro del chat cada 500ms

                    // Solicita el registro del chat
                    function cargar_log()
                    {
                        var datos = {user_actual:<?php echo $user_actual; ?>,   // Se envían las IDs del cliente
                                    user_otro:<?php echo $user_otro; ?>}        // y el usuario con el que se chatea

                        $.get("procesarchat.php", datos, mostrar_chat); // Solicita los datos
                    }

                    // Muestra la conversación en el contenedor
                    function mostrar_chat(datos_rec)
                    {
                        $("#caja_chat").html(datos_rec);
                    }

                    // Envía los mensajes del usuario cliente al servidor
                    $("#formulario").submit(function(event) {

                        event.preventDefault(); // Cancela el comportamiento predeterminado del formulario

                        var datos_env = $(this).serialize();    // Envuelve los datos del formulario

                        $.post("procesarchat.php", datos_env);  // Envía los datos

                        $("#mensaje").val("");  // Limpia el campo texto del formulario

                    });
                })
            </script>

        </head>
        <body style = "text-align:center">
                
            <!-- Barra de navegación -->
            <?php include "barra_navegacion.php"; ?>

            <h1 style = "margin-top:75px;">Chat</h1>
<?php
            $username_otro = mysqli_fetch_array(mysqli_query($link, "SELECT username FROM users WHERE id = '$user_otro'"))['username'];
            echo "Chateando con <i>" . $username_otro . "</i>";
?>
            <!-- Contenedor de conversación -->
            <div class = "container" id = "caja_chat" style = "border:1px solid black; padding:20px; margin-top:10px;"></div>
            <br>

            <!-- Formulario de envío -->
            <form id = "formulario" method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $user_otro; ?>">
                <input type = "text" id = "mensaje" name = "mensaje" placeholder = "Escribe un mensaje..." value = "">
                <input type = "hidden" id = "user_actual" name = "user_actual" value = "<?php echo $user_actual; ?>">
                <input type = "hidden" id = "user_otro" name = "user_otro" value = "<?php echo $user_otro; ?>">
                <input type = "submit" value = "Enviar">
            </form>
        </body>
        </html>

<?php
    }
?>