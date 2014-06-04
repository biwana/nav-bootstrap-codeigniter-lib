nav-bootstrap-codeigniter-lib
=============================

Nav list builder library for Bootstrap 3 using CodeIgniter. 

Allows for easy integration of the Nav component from Bootstrap 3 as a CodeIgniter library.

Bootstrap JS: Nav Component Documentation
http://getbootstrap.com/components/#nav

CodeIgniter Documentation
http://ellislab.com/codeigniter/user-guide/

Full nav-bootstrap-codeigniter-lib Documentation
http://brianiwana.com/utilities/detail/nav

Installation
----

Copy /libraries/Nav.php into your library folder (default: /application/libraries)

Use
----
CodeIgniter Code:

<code>
$list = array(
  'home' => array('label' => 'HOME'), // they key is the implied location of the link
  'about' => array('label' => 'ABOUT', 'location' => 'some-other-location'),
  'admin' => array('label' => 'ADMIN'),
  'admin/settings' => array('label' => 'SETTINGS', 'parent_id' => 'admin'),
);
$page_id = 'about'; // the active item from the list above 

$this->load->library('nav');
$this->nav->initialize($config); // optional config file, see full documentation
$this->nav->render($list, $page_id);
</code>

Output HTML:

<code>
<ul class="nav navbar-nav">
  <li><a href="http://brianiwana.com/home">HOME</a></li>
  <li><a href="http://brianiwana.com/some-other-location">ABOUT</a></li>
  <li class='dropdown'>
    <a href="http://brianiwana.com/#" class="dropdown-toggle" data-toggle="dropdown">ADMIN <span class='caret'></span></a>
    <ul class="dropdown-menu">
      <li><a href="http://brianiwana.com/admin/settings">SETTINGS</a></li>
    </ul>
  </li>
</ul>
</code>