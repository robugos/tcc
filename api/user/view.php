<?php 
	require_once('functions.php'); 
	view($_GET['id']);
	get_interest();
	$interesses = explode(",", str_replace(array("[", "]", " "), "", $event['CATEGORY']));;
?>

<?php include(HEADER_TEMPLATE); ?>

<h2><?php echo $event['NAME']; ?></h2>
<hr>

<?php if (!empty($_SESSION['message'])) : ?>
	<div class="alert alert-<?php echo $_SESSION['type']; ?>"><?php echo $_SESSION['message']; ?></div>
<?php endif; ?>

  <div class="row">
    <div class="form-group col-md-6">
      <figure class="figure">
	  <img src="<?php echo $event['COVER']; ?>" class="figure-img img-fluid rounded" alt="<?php echo $event['ADCOVER']; ?>">
	  <figcaption class="figure-caption text-center"><?php echo $event['ADCOVER']; ?></figcaption>
	</figure>
    </div>
    <div class="form-group col-md-6">
      <dl class="dl-horizontal">
	<dt>Nome do evento:</dt>
	<dd><?php echo $event['NAME']; ?></dd>

	<dt>Local de realização:</dt>
	<dd><?php echo $event['PLACE']; ?></dd>

	<dt>Descrição do evento:</dt>
	<dd><?php echo $event['DESCRIPTION']; ?></dd>

	<dt>Data de início:</dt>
	<dd><?php echo date('d/m/Y h:i', strtotime($event['START_TIME'])); ?></dd>

	<dt>Data de término:</dt>
	<dd><?php echo date('d/m/Y h:i', strtotime($event['END_TIME'])); ?></dd>

	<dt>Data de criação:</dt>
	<dd><?php echo date('d/m/Y h:i', strtotime($event['CREATED'])); ?></dd>

	<dt>Data da última atualização:</dt>
	<dd><?php echo date('d/m/Y h:i', strtotime($event['UPDATED'])); ?></dd>

	<dt>Criado por:</dt>
	<dd><?php echo $event['AUTHOR']; ?></dd>
	
	<dt>Categorias:</dt>
	<dd>
		  <?php if ($interests) : ?>
<?php foreach ($interests as $interest) : ?>
		<?php if (in_array($interest['ID'], $interesses)) {echo $interest['NAME'].";";} ?>
<?php endforeach; ?>
<?php endif; ?>
		</dd>
</dl>

<div id="actions" class="row">
	<div class="col-md-8 text-right">
	  <a href="edit.php?id=<?php echo $event['ID']; ?>" class="btn btn-primary">Editar</a>
	  <a href="index.php" class="btn btn-default">Voltar</a>
	</div>
</div>
    </div>
  </div>

<?php include(FOOTER_TEMPLATE); ?>