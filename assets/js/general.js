/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function popup_link(link, height, width){
	if(!height){
		height = 500;
	}
	if(!width){
		width = 800;
	}

	window.open(link, null, "height=" + height + ",width=" + width + ",status=yes,toolbar=no,menubar=no,location=no, scrollbars=yes, resizable=yes");
}