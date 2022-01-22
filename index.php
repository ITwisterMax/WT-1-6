<?

// Применение шаблона и отображение страницы
function view_page($page)
{
    $template = file_get_contents($page);
    $template = preg_replace("/({FILE ?= ?\")([a-zA-z.0-9_]*)(\"})/",
            "<? include \"$2\" ?>", $template);
    file_put_contents("temp", $template);
    include "temp";
    unlink("temp");
}

// Вход в аккаунт
function log_in_account()
{
    include "db.php";
    $link = get_connection() or die("Connection error!");
    $query = mysqli_query($link, "SELECT * FROM `accounts_list` WHERE account_login='{$_POST["login"]}'");
    $data = mysqli_fetch_assoc($query);
    if ($data && ($data["account_password"] == $_POST["password"]))
        echo "<script>document.location.href=\"page.html\"</script>";
    mysqli_free_result($query);
}

// Регистрация аккаунта
function reg_new_account()
{
    session_start();
    if (($_POST["create_name"] != "") && ($_POST["create_email"] != "") &&
    ($_POST["create_login"] != "") && ($_POST["create_password"] != "") && ($_POST["captcha"] == $_SESSION["rand_number"])) 
    {
        include "db.php";
        $link = get_connection() or die("Connection error!");
        $query = mysqli_query($link, "SELECT * FROM `accounts_list` WHERE account_login='{$_POST["create_login"]}'");
        $data = mysqli_fetch_assoc($query);
        if (!$data)
        {
            $query = mysqli_query($link, "INSERT INTO `accounts_list`(`account_name`, `account_email`, `account_login`, `account_password`)
                VALUES ('{$_POST["create_name"]}','{$_POST["create_email"]}','{$_POST["create_login"]}','{$_POST["create_password"]}')");
            echo "<script>document.location.href=\"index.php\"</script>";
        }
        mysqli_free_result($query);
    }
}

// Регистрация или вход в аккаунт
if (isset($_POST["let_log_in"]))
    log_in_account();
elseif (isset($_POST["let_reg"]))
    reg_new_account();

// Загрузка нужной стартовой страницы
if (isset($_POST["reg"]) || isset($_POST["let_reg"]))
    view_page("reg.html");
else
    view_page("log_in.html");
