<?php ?>
.elgg-menu-site .elgg-child-menu {

    border-right: 1px solid #999999;
    border-left: 1px solid #999999;
    border-bottom: 1px solid #999999;

    -webkit-box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.25);
	-moz-box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.25);
	box-shadow: 1px 1px 1px rgba(0, 0, 0, 0.25);

	-webkit-border-radius: 0 0 4px 4px;
	-moz-border-radius: 0 0 4px 4px;
	border-radius: 0 0 4px 4px;

	min-width: 100%;
    left: -1px;
    position: absolute;
}
.elgg-menu-site .elgg-child-menu a {
 	background-color: white;
    color: #555555;
    font-weight: bold;
    height: 20px;
    padding: 3px 13px 0;
    white-space: nowrap;
}

.elgg-menu-site .elgg-child-menu > li:last-child > a {
    -webkit-border-radius: 0 0 4px 4px;
	-moz-border-radius: 0 0 4px 4px;
	border-radius: 0 0 4px 4px;
}

.elgg-menu-site .elgg-child-menu a:hover {
	text-decoration: none;
	background: #4690D6;
	color: white;
}

.elgg-menu-site .elgg-child-menu li .elgg-menu {
	display: none;
	border-top: 1px solid #999999;
	-webkit-border-radius: 0 4px 4px 0;
	-moz-border-radius: 0 4px 4px 0 ;
	border-radius: 0 4px 4px 0;
}

.elgg-menu-site .elgg-child-menu li .elgg-menu li a {
 	-webkit-border-radius: 0 4px 4px 0;
	-moz-border-radius: 0 4px 4px 0 ;
	border-radius: 0 4px 4px 0;
}

.elgg-menu-site .elgg-child-menu li:hover > ul.elgg-menu {
	display: block;
}

.elgg-menu-site .elgg-child-menu > li > .elgg-child-menu {
	position: absolute;
	top: 0px;
}

.menu_builder_add_link{
	text-align: center;
}

.elgg-menu-site > li.elgg-menu-item-menu-builder-add > a,
.elgg-menu-site > li.elgg-menu-item-menu-builder-edit-mode > a,
.elgg-menu-site > li.elgg-menu-item-menu-builder-switch-context > a,
.elgg-menu-site > li.elgg-menu-item-menu-builder-add:hover > a,
.elgg-menu-site > li.elgg-menu-item-menu-builder-edit-mode:hover > a,
.elgg-menu-site > li.elgg-menu-item-menu-builder-switch-context:hover > a {
	background: none;
	padding-left: 3px;
	padding-right: 3px;

	 -webkit-box-shadow: 0px 0px;
	-moz-box-shadow: 0px 0px;
	box-shadow: 0px 0px;
}

.menu-builder-edit-menu-item {
	display: none;
	vertical-align: top;
}

.elgg-menu-site li:hover > a > .menu-builder-edit-menu-item {
	display: inline-block;
	margin-left: 10px;
}

.menu-builder-context-logged-out li,
.menu-builder-context-logged-in-normal .menu-builder-access-0,
.menu-builder-access--5 {
	display: none;
}

.menu-builder-context-all li,
.menu-builder-context-logged-out .menu-builder-access-2,
.menu-builder-context-logged-out .menu-builder-access--5 {
	display: list-item;
}

.elgg-menu-item-menu-builder-switch-context,
.elgg-menu-item-menu-builder-add,
.elgg-menu-item-menu-builder-edit-mode {
	display: list-item !important;
}