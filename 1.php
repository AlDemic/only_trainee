<?php
    session_start();

    require_once __DIR__ . '/disk_actions.php';
    require __DIR__ . '/vendor/autoload.php';

    $token = 'OAthTokenHere' ?? '';
    
    //how many files takes from disk
    $takeFilesFromDisk = 15;

    $resources = []; //take files from disk 

    /*
        Загрузка файлов с диска работает через lazy-loading, поэтому собираем ДО рендера,
        чтобы ловить и обрабатывать эксепшены вовремя
    */ 
    try {
        $disk = new Arhitector\Yandex\Disk($token);

        //prepare files
        foreach($disk->getResources($takeFilesFromDisk) as $resLazy) {
            $resources[] = $resLazy;
        }
    } catch (Arhitector\Yandex\Client\Exception\UnauthorizedException $exc) {
        $_SESSION['errorCritical'] = 'Пользователь не авторизирован.';
    } catch (Exception $exc) {
        $_SESSION['errorCritical'] = 'Ошибка при загрузке ресурсов с диска.';
    }

    //CHECK SELECTED ACTION BY USER
    if($_SERVER['REQUEST_METHOD'] === 'POST' && $token !== '') {
        $action = $_POST['action'] ?? '';

        switch($action) {
            case 'deleteSoft':
                $fPath = $_POST['filePath'] ?? '';
                deleteFile($disk, $fPath, true);
                break;
            case 'delete':
                $fPath = $_POST['filePath'] ?? '';
                deleteFile($disk, $fPath, false);
                break;
            case 'load':
                $file = $_FILES['file'] ?? '';
                loadToDisk($disk, $file);
                break;
            case 'rename':
                $filePath = (trim($_POST['filePath'])) ?? '';
                $fileNewName = (trim($_POST['fileNewName'])) ?? '';
                renameFile($disk, $filePath, $fileNewName);
                break;
            default:
                $_SESSION['error'] = 'Выберите поддерживаемое действие для файла.';
        }

    }

?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ya.Disk App</title>
</head>

<body>
    <h1>WEB App for yandex disk management</h1>

    <?php include_once __DIR__ . '/msg_block.php'; ?>

    <?php if(!isset($_SESSION['errorCritical'])): ?>
        <table>
            <thead>
                <tr>
                    <th>Название файла</th>
                    <th>Path файла</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($resources as $res):
                ?>
                    <tr>
                        <?php
                            $fileName = $res->name;
                            $filePath = $res->path;
                        ?>
                        <td><?= htmlspecialchars($fileName) ?></td>
                        <td><?= htmlspecialchars($filePath) ?></td>

                        <!-- delete to trash block -->
                        <td>
                            <form action="1.php" method="POST" >
                                <input type="hidden" name="action" value="deleteSoft" />
                                <input type="hidden" name="filePath" value="<?= $filePath ?>" />

                                <button type="submit">в корзину</button>
                            </form>
                        </td>
                        <!-- delete total block -->
                        <td>
                            <form action="1.php" method="POST" >
                                <input type="hidden" name="action" value="delete" />
                                <input type="hidden" name="filePath" value="<?= $filePath ?>" />

                                <button type="submit">удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div>
            <h3>Загрузить файл на диск</h3>

            <form action="1.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="load" />
                <input type="file" name="file">
                
                <button type="submit">Загрузить на диск</button>
            </form>
        </div>

        <div>
            <h3>Переименовать файл</h3>

            <form action="1.php" method="POST">
                <input type="hidden" name="action" value="rename" />
                <input type="text" name="filePath" placeholder="Укажите путь и название файла" required />
                <input type="text" name="fileNewName" placeholder="Укажите новое название файла" required />
                
                <button type="submit">Переименовать</button>
            </form>
        </div>
    <?php else: 
        
        $msg = $_SESSION['errorCritical'];
        echo "<h3>$msg</h3>";
        echo "<p>Пожалуйста, устраните ошибки для работы с диском.</p>";

        unset($_SESSION['errorCritical']);
        endif;
    ?>
</body>
</html>



    
