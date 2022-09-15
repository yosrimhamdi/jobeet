[
<?php foreach($categories as $id =>$category): ?>
    {
        "id": <?php echo $category->getId(); ?>,
        "name": "<?php echo $category->getName(); ?>"
    } <?php
            if ($id != count($categories) - 1) {
                echo ",";
            }
        ?>
<?php endforeach; ?>
]