<!DOCTYPE HTML>
<html>
    <head>
        <title>ORM</title>
        
    </head>
    <body>
        <table border="1">
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>City</th>
                <th>Faculties</th>
            <tr>
            <?php foreach ($university as $univer) : ?>
                <tr>
                    <td><?= $univer->getId(); ?></td>
                    <td><?= $univer->getName(); ?></td>
                    <td><?= $univer->getCity(); ?></td>
                    <td><?= $univer->getFaculties(); ?></td>
                </tr>
            <?php endforeach;?>
        </table>
    </body>
</html>