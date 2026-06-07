<?php

    function deleteFile($disk, $fPath, $deleteSoft = true) {
        //check if empty
        if($fPath === '') {
            $_SESSION['error'] = 'Путь к файлу не должен быть пустым.';
            header("Location: 1.php");
            exit;
        }

        try {
            $resource = $disk->getResource($fPath);

            $delete = ($deleteSoft)
                ? $resource->delete()   //move to trash
                : $resource->delete(true); //delete file totally

            if($delete) $_SESSION['msg'] = ($deleteSoft) 
                ? 'Файл перемещён в корзину.' 
                : 'Файл полностью удалён с диска';
                
            header('Location: 1.php');
            exit;
        } catch (Arhitector\Yandex\Client\Exception\NotFoundException $exc) {
            $_SESSION['error'] = 'Файл не найден';
            header('Location: 1.php');
            exit;
        } catch (Arhitector\Yandex\Client\Exception\UnauthorizedException $exc) {
            $_SESSION['error'] = 'Пользователь не авторизирован.';
            header('Location: 1.php');
            exit;
        } catch (Exception $exc) {
            $_SESSION['error'] = 'Ошибка в процессе удаления.';
            header('Location: 1.php');
            exit;
        }
    }

    function loadToDisk($disk, $file) {
        //check if empty
        if(!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK || $file === '') {
            $_SESSION['error'] = 'Не выбран файл.';
            header("Location: 1.php");
            exit;
        }

        try {
            //get resource by name
            $fileName = $file['name'];
            $resource = $disk->getResource($fileName);

            $loaded = $resource->upload($file['tmp_name']);

            if($loaded) $_SESSION['msg'] = 'Файл успешно добавлен на диск.';
            
            header('Location: 1.php');
            exit;
        } catch (Arhitector\Yandex\Client\Exception\UnauthorizedException $exc) {
            $_SESSION['error'] = 'Пользователь не авторизирован.';
            header('Location: 1.php');
            exit;
        } catch (Arhitector\Yandex\Disk\Exception\AlreadyExistsException $exc) {
            $_SESSION['error'] = 'Файл уже существует.';
            header('Location: 1.php');
            exit;
        } catch (Exception $exc) {
            $_SESSION['error'] = 'Ошибка в процессе добавления.';
            header('Location: 1.php');
            exit;
        }
    }

    function renameFile($disk, $filePath, $fileNewName) {
        //check if empty
        if($filePath === '' || $fileNewName === '') {
            $_SESSION['error'] = 'Должны указать путь к файлу и новое название.';
            header("Location: 1.php");
            exit;
        }

        try {
            $resource = $disk->getResource($filePath);

            $dir = dirname($filePath);

            $newPath = $dir . '/' . $fileNewName;

            $updated = $resource->move($newPath);

            if($updated) $_SESSION['msg'] = 'Файл успешно переименован.';
            
            header('Location: 1.php');
            exit;
        } catch (Arhitector\Yandex\Client\Exception\UnauthorizedException $exc) {
            $_SESSION['error'] = 'Пользователь не авторизирован.';
            header('Location: 1.php');
            exit;
        } catch (Arhitector\Yandex\Disk\Exception\AlreadyExistsException $exc) {
            $_SESSION['error'] = 'Файл уже существует.';
            header('Location: 1.php');
            exit;
        } catch (Exception $exc) {
            $_SESSION['error'] = 'Ошибка в процессе добавления.';
            header('Location: 1.php');
            exit;
        }
    }