<?php /* Smarty version 2.6.26, created on 2009-08-12 17:47:44
         compiled from _common/footer.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'date_format', '_common/footer.html', 3, false),)), $this); ?>

<div style="border-top:1px solid gray; text-align:right; font-size:80%">
	CopyRight (C) <?php echo ((is_array($_tmp=time())) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y") : smarty_modifier_date_format($_tmp, "%Y")); ?>
 <a href="http://wingphp.net" target="_blank">WingPHP</a>, All Right Reserved.
</div>