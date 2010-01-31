<?php
/*
 * kakurc, room control software
 * Copyright (C) 2010
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *  
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();
require_once('init.php'); 
?>
<html>
	<head>
		<title><?php echo $settings['general.title'] ?></title>
		
		<link rel="stylesheet" href="css/default.css?<?php echo $version;?>" type="text/css" />
		
		<script type="text/javascript" src="js/jquery-1.4.min.js?<?php echo $version;?>"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8rc1.custom.min.js?<?php echo $version;?>"></script>
		<script type="text/javascript" src="js/KakuRC.js?<?php echo $version;?>"></script>
		
		<script type="text/javascript">
		$(document).ready(function(){
			KakuRC.init();

			KakuRC.setWebcamsInterval  ( <?php echo($settings["webcams.interval"]);           ?> );
			
			<?php
				foreach ($settings['menu.options'] as $optionId => $option)
				{
					foreach($option['types'] as $buttonId)
					{
						echo('KakuRC.addMenuHandler(' . "'menu-" . trim($optionId)
							. "','menu-" . trim($optionId) . '-' . trim($buttonId) . "', '"
							. trim($optionId) . "','" . trim($buttonId) . "');"); 
					}
				}
				
				foreach ($settings['webcams.cams'] as $nr => $cam)
				{
					echo('KakuRC.addWebcam("webcam-' . $nr . '", "' . $cam['url'] . '");');
				}
			?>
			
			KakuRC.start();
		});
		</script>
	</head>
	
	<body>
		<div id="container">
			<h1><?php echo $settings['general.title'] ?></h1>
			
			<?php 
				if ($settings['general.demo'])
				{
					echo('<div id="demo">' . $settings['general.demomessage'] . '</div>');
				}
			?>
			
			<div id="menu">
				<?php 
					foreach ($settings['menu.titles'] as $menuId => $title)
					{
						echo('<h2>' . $title . '</h2><ul>');
						foreach ($settings['menu.options'] as $optionId => $option)
						{
							if ($option['parent'] == $menuId)
							{
								echo('<li id="menu-' . trim($optionId) . '">' . $option['title']);
								$buttons = array_reverse($option['types']);
								foreach($buttons as $buttonId)
								{
									$button = $settings['menu.types'][$buttonId];
									echo('<a id="menu-' . trim($optionId) . '-' . trim($buttonId) . 
										'" class="button ' . $button['class'] . '" href="#">');
									echo($button['title'] . '</a>');
								}
								echo('<img class="loader" src="img/loader.gif"/>');
								echo('</li>');
							}
						}
						echo('</ul>');
					}
				?>
			</div>
			
			<div id="webcams">
				<div class="slider">
					<div id="webcam-interval">1 sec</div>
					<div id="webcam-slider">
						<div id="webcam-timer"></div>
					</div>
				</div>
				<?php 
					$styleDivAllowed   = array('top', 'bottom', 'left', 'right', 'float');
					$styleImageAllowed = array('width', 'height', 'max-width', 'max-height', 'min-width', 'min-height');
					foreach ($settings['webcams.cams'] as $nr => $cam)
					{
						$styleDiv='';
						$styleImage='';
						
						foreach($styleDivAllowed as $element) 
						{
							if (isset($cam[$element])) $styleDiv .= $element . ': ' . $cam[$element] . '; ';
						}
						
						foreach($styleImageAllowed as $element) 
						{
							if (isset($cam[$element])) $styleImage .= $element . ': ' . $cam[$element] . '; ';
						}
						
						if (isset($cam['top']) || isset($cam['bottom']) || isset($cam['left']) || isset($cam['right']))
						{
							$styleDiv .= 'position: fixed; ';
						}
						
						echo('<div class="container" style="' . $styleDiv . '">');
						echo('<div class="holder">');
						echo('<div class="border"></div>');
						echo('<div id="webcam-' . $nr . '" >');
						echo('<img class="webcam" style="' . $styleImage . '" src="' . $cam['url'] . '"/>');
						echo('</div>');
						echo('<div class="title">' . $cam['title'] . '</div>');
						echo('<div class="shadow" id="shadow-webcam-' . $nr . '"></div>');
						echo('<img class="error"  id="error-webcam-'  . $nr . '" src="img/webcam_error.png?<?php echo $version;?>"/>');
						echo('</div>');
						echo('</div>');
					}
				?>
			</div>
		</div>
	</body>
</html>