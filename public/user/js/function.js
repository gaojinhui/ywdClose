//标签页切换
function setTab(titleid,count,totalCount,class_name,url,itemscode){
  for(var i=1;i<=totalCount;i++){
     document.getElementById(titleid+i).className=i==count?class_name:"";
     document.getElementById("con_"+titleid+i).style.display=i==count?"block":"none";
     var moreId_obj=document.getElementById(titleid);
     if(moreId_obj!=null)moreId_obj.innerHTML="<a href='"+url+"'>更多...</a>";
  }
}
//
function levelLine(){
	var leftObj=document.getElementById( "l");
	var rightObj=document.getElementById( "r");
	if(leftObj!=null&&rightObj!=null){
		if (rightObj.scrollHeight < leftObj.scrollHeight){ 
		 	rightObj.style.height=leftObj.scrollHeight+"px"; 
		}else{ 
		 	leftObj.style.height=rightObj.scrollHeight+"px"; 
		 } 
	 }   
}
