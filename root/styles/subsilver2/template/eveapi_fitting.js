function chooseTab(obj, tab){
	var loadoutdiv = obj.parentNode.parentNode.parentNode.childNodes[2];
	var exportdiv = obj.parentNode.parentNode.parentNode.childNodes[1];
	
	switch(tab){
		case 'loadout':
			exportdiv.style.display = 'none';
			loadoutdiv.style.display = 'block';
		break;
		case 'export':
			exportdiv.style.display = 'block';
			loadoutdiv.style.display = 'none';
		break;
	}
}