<?php
?>
.elgg-menu-site .elgg-child-menu {
	background-color: #fff;
	border: 1px solid #999;
	border-width:  0 1px 1px;
	box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.25);
	display: none;
	position: absolute;
	left: -1px;
}

.elgg-menu-site > li > ul .elgg-child-menu {
	left: 180px;
	top: -1px;
	width: 180px;
	display: none;
	position: absolute;
	border-width:  1px;
}

.elgg-menu-site > li li:hover > .elgg-child-menu {
	display: block;
}

.elgg-menu-site .elgg-child-menu,
.elgg-menu-site .elgg-child-menu li {
	width: 180px;
}
.elgg-menu-site .elgg-child-menu > li > a {
	padding: 3px 13px;
	text-decoration: none;
	
	background: none;
    border-radius: 0;
    box-shadow: none;
    color: #555;
    font-weight: bold;
}

.elgg-menu-site .elgg-child-menu li > a:hover,
.elgg-menu-site .elgg-child-menu li > a:focus {
    background: #4690d6;
    color: white;
}
<?php
if (!elgg_is_active_plugin('aalborg_theme')) {
	return;
}
?>

.elgg-menu-site .elgg-child-menu {
	left: 0px;
	top: 47px;
	width: 180px;
	display: none;
	position: absolute;
	
	background-color: #fff;
	border: 1px solid #dedede;
	border-radius: 0 0 3px 3px;
	box-shadow: 1px 3px 5px rgba(0, 0, 0, 0.25);
	z-index: 1;
}

.elgg-menu-site > li li:hover > .elgg-child-menu {
	display: block;
}

.elgg-menu-site .elgg-child-menu li {
	width: 180px;
}
.elgg-menu-site .elgg-child-menu > li > a {
	padding: 10px 20px;
	background-color: #FFF;
	color: #444;
	text-decoration: none;
	font-weight: normal;
}
.elgg-menu-site .elgg-child-menu > li:last-child > a,
.elgg-menu-site .elgg-child-menu > li:last-child > a:hover {
	border-radius: 0 0 3px 3px;
}
.elgg-menu-site .elgg-child-menu > li.elgg-state-selected > a,
.elgg-menu-site .elgg-child-menu > li > a:hover {
	background-color: #F0F0F0;
	color: #444;
}
