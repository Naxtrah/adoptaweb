<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'AdoptaWeb' ?></title>
  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/assets/favicon.png">
  
    <link rel="stylesheet" href="<?= CSS_URL ?>styles.css">    
 
    <?php if (isset($estilos_adicionales)) echo $estilos_adicionales; ?>
   
    <meta name="description" content="AdoptaWeb - Plataforma para adoptar animales rescatados">
    <meta name="keywords" content="adopción, animales, mascotas, perros, gatos, rescate">
    <meta name="author" content="AdoptaWeb">
</head>
<body>