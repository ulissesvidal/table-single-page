/**
*Copyright (c) 2009 Gomilsek-informatika.
*All rights reserved. This program and the accompanying materials
*are made available under the terms of the GNU Public License v2.0
*which accompanies this distribution, and is available at
*http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
*Contributors:
*	Gomilsek-informatika  (initial API and implementation)
*Contact:
*	customers@toolsjx.com
 */
function insertGrid(IdGrid){
	var tag = '{insertgrid ID = '+IdGrid+'_'+RandLCChar()+RandLCChar()+'}';
	window.parent.jInsertEditorText(tag, 'jform_articletext');
	window.parent.SqueezeBox.close();
	return false;
}

function RandLCChar()
{
   return String.fromCharCode(97 + Math.round(Math.random() * 25));
}

