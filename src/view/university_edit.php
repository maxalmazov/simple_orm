<?php //TODO: сделать развитую структуру темплейтов, футеры хедеры, лучше всего подключить twig ?>
<html>
<head>
    <title>ORM</title>

</head>
<body>
<tаblе>
    <tr>
        <td>
            <form action="" method="post">
                <p>University name: <input type="text" name="name" value="<?= $university->getName()?>" /></p>
                <p>City: <input type="text" name="city" value="<?= $university->getCity()?>"/></p>
                <p>Faculties: <input type="text" name="faculties" value="<?php echo !is_null($university->getFaculties()) ? $university->getFaculties() : 'none' ;?>"/></p>
                <input type="hidden" name="id" />
                <p><input type="submit" /></p>
            </form>
        </td>
    </tr>
</tаblе>
</body>
</html>
