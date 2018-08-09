
//火狐innerText
if(window.navigator.userAgent.toLowerCase().indexOf("msie")==-1)
{
	HTMLElement.prototype.__defineGetter__("innerText",function(){
		var anyString="";
		var childs=this.childNodes;
		for(var i=0;i<childs.length;i++)
		{
			if(childs[i].nodeType==1)
			{
				anyString+=childs[i].tagName=="BR"?"\n":childs[i].textContent;
			}else if(childs[i].nodeType==3)
			{
				anyString+=childs[i].nodeValue;
			}
		}
		return anyString.replace(/[\	\r\n\ ]/g,"");
	});
	
	HTMLElement.prototype.__defineSetter__("innerText",function(sText){
		this.textContent=sText;
	});
	
}


