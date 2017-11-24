<?php
?>
.myvox-menu-site {
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    float: left;
    left: 0;
    top: 0;
    position: relative;
    z-index: 50;
    width: 100%;
}

.myvox-menu-site .myvox-child-menu {
	background-color: #fff;
	border: 1px solid #999;
	border-width:  0 1px 1px;
	box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.25);
	display: none;
	position: absolute;
	left: -1px;
}

.myvox-menu-site > li > ul .myvox-child-menu {
	left: 180px;
	top: -1px;
	width: 180px;
	display: none;
	position: absolute;
	border-width:  1px;
}

.myvox-menu-site > li li:hover > .myvox-child-menu {
	display: block;
}

.myvox-menu-site .myvox-child-menu,
.myvox-menu-site .myvox-child-menu li {
	width: 180px;
}
.myvox-menu-site .myvox-child-menu > li > a {
	padding: 3px 13px;
	text-decoration: none;
	
	background: none;
    border-radius: 0;
    box-shadow: none;
    color: #555;
    font-weight: bold;
}

.myvox-menu-site .myvox-child-menu li > a:hover,
.myvox-menu-site .myvox-child-menu li > a:focus {
    background: #4690d6;
    color: white;
}
<?php
if (!myvox_is_active_plugin('aalborg_theme')) {
	return;
}
?>

.myvox-menu-site .myvox-child-menu {
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

.myvox-menu-site > li li:hover > .myvox-child-menu {
	display: block;
}

.myvox-menu-site .myvox-child-menu li {
	width: 180px;
}
.myvox-menu-site .myvox-child-menu > li > a {
	padding: 10px 20px;
	background-color: #FFF;
	color: #444;
	text-decoration: none;
	font-weight: normal;
}
.myvox-menu-site .myvox-child-menu > li:last-child > a,
.myvox-menu-site .myvox-child-menu > li:last-child > a:hover {
	border-radius: 0 0 3px 3px;
}
.myvox-menu-site .myvox-child-menu > li.myvox-state-selected > a,
.myvox-menu-site .myvox-child-menu > li > a:hover {
	background-color: #F0F0F0;
	color: #444;
}

.myvox-menu-site li.right{
    float: right;
}