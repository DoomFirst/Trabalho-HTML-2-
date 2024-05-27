<?php
$errors = []; // Array para armazenar mensagens de erro

// Verifica se o formulário de cadastro foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validar e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "O e-mail inserido não é válido.";
    }

    // Validar outros campos (por exemplo, se estão vazios)
    if (empty($username)) {
        $errors[] = "Por favor, insira um nome de usuário.";
    }

    if (empty($password)) {
        $errors[] = "Por favor, insira uma senha.";
    }

    // Se não houver erros, processa os dados
    if (empty($errors)) {
        // Adiciona os dados ao arquivo CSV
        addDataToFile([$username, $email, $password]);
    }
}

// Excluir cadastro se o botão excluir for clicado
if (isset($_POST['delete'])) {
    $row = $_POST['row'];
    deleteRow($row);
}

// Atualizar cadastro se o botão salvar for clicado
if (isset($_POST["update"])) {
    $row = $_POST['row'];
    $username = $_POST['editUsername'];
    $email = $_POST['editEmail'];
    $password = $_POST['editPassword']; // Captura a senha
    updateData($row, $username, $email, $password);
}

// Função para adicionar dados ao arquivo CSV
function addDataToFile($data) {
    $file = fopen("data.csv", "a");
    fputcsv($file, $data);
    fclose($file);
}

// Função para excluir uma linha do arquivo CSV
function deleteRow($row) {
    $rows = file('data.csv');
    unset($rows[$row]);
    file_put_contents('data.csv', implode('', $rows));
}

// Função para obter os dados de uma linha específica
function getData($row) {
    $file = file("data.csv");
    $data = explode(",", $file[$row]);
    return array_map('trim', $data);
}

// Função para atualizar os dados no arquivo CSV
function updateData($row, $username, $email, $password) {
    $file = file("data.csv");
    $data = getData($row);
    $data[0] = $username;
    $data[1] = $email;
    $data[2] = $password;
    $file[$row] = implode(",", $data) . PHP_EOL;
    file_put_contents('data.csv', implode('', $file));
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Demo Sonic</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="./IMG/Sonic 1.png" alt="logo do sonic">
        <img src="./IMG/32806c94576ec8d61ce94d1f3582fa40-removebg-preview.png" alt="logo do sonic">
        <img src="./IMG/egs-sonicorigins-sega-ic1-400x400-dab1667cb666-removebg-preview.png" alt="logo do sonic">
        <img src="./IMG/png-transparent-sonic-adventure-2-battle-sonic-the-hedgehog-sonic-unleashed-others-removebg-preview.png" alt="logo do sonic">
    </header>
    <div>
        <div id="background">
            <video loop autoplay muted>
                <source src="./VIDEO/WhatsApp Video 2024-05-17 at 13.08.41 (1).mp4" type=video/mp4>
            </video>
        </div>
    </div>
    <div class="container">
        <form id="cadastroForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h2>Cadastro</h2>
            <div class="input-group">
                <label for="username">Nome de usuário:</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="input-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <button type="submit" name="submit">Cadastrar</button>
            <button type="button" onclick="clearForm()">Limpar Formulário</button>
        </form>
        <div class="cadastros-table">
            <h2>Cadastros</h2>
            <table>
                <tr>
                    <th>Nome de usuário</th>
                    <th>Email</th>
                    <th>Senha</th>
                    <th>Ações</th>
                </tr>
                <?php
                // Lê cada linha do arquivo CSV
                $file = fopen("data.csv", "r");
                $row = 0;
                while (($data = fgetcsv($file)) !== false) {
                    echo "<tr>";
                    foreach ($data as $key => $value) {
                        echo "<td>$value</td>";
                    }
                    // Adicionar botões de editar e excluir
                    echo "<td><button onclick='editRow($row)'>Editar</button></td>";
                    echo "<td><form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "'><input type='hidden' name='row' value='$row'><button type='submit' name='delete'>Excluir</button></form></td>";
                    echo "</tr>";
                    $row++;
                }
                fclose($file);
                ?>
            </table>
        </div>
    </div>
    <form id="editForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" id="editRow" name="row" value="">
        <input type="hidden" name="update" value="1">
        <div class="input-group">
            <label for="editUsername">Nome de usuário:</label>
            <input type="text" id="editUsername" name="editUsername" required autocomplete="username">
        </div>
        <div class="input-group">
            <label for="editEmail">Email:</label>
            <input type="email" id="editEmail" name="editEmail" required autocomplete="email">
        </div>
        <div class="input-group">
            <label for="editPassword">Nova Senha:</label>
            <input type="password" id="editPassword" name="editPassword" autocomplete="new-password">
        </div>
        <button type="submit">Salvar</button>
        <button type="button" onclick="cancelEdit()">Cancelar</button>
    </form>
    <script>
        function editRow(row) {
            var cells = document.querySelectorAll(".cadastros-table tr")[row + 1].querySelectorAll("td");
            document.getElementById("editRow").value = row;
            document.getElementById("editUsername").value = cells[0].innerText;
            document.getElementById("editEmail").value = cells[1].innerText;
            document.getElementById("editForm").style.display = "block";
        }

        function cancelEdit() {
            document.getElementById("editForm").style.display = "none";
        }

        function clearForm() {
            var inputs = document.querySelectorAll("#cadastroForm input[type='text'], #cadastroForm input[type='email'], #cadastroForm input[type='password']");
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].value = "";
            }
        }
    </script>
</body>
</html>
