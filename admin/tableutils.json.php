<script>
/**
* -------------------------------------------
* 执信青年志愿者协会 加载过滤器按钮的半json
* Author: @zhangjingye03
* License: GPLv3
* Copyright (C) 2016
* -------------------------------------------
*/
/*
因为编辑器不支持直接在php中码json，所以采用这样的折中方案
编码规范：
  id      为过滤器按钮的id，随便起
  title   为过滤器的标题，即显示在按钮上的文字
  choice  为可供过滤的选项，因为有些选项是动态的，所以用php生成，生成的时候注意引号和逗号
  default 为默认选项
  onclick 为点击后执行的函数名称
  ignore  为忽略更改（如导出Excel这种半功能半选项的按钮）
本文件中列出常用且公用的几个filter，如果只有一个页面使用可以在mkfilters()函数运行前加入
filters[filters.length]=({
    "id":"xxx",
    "title":"xxxx",
    "choice":["a","b","c"],
    "default":"a",
    "onclick":"xxxxx()"
});
然后在mkfilters中加入此filter的id即可
*/
filters=(
  [
    {
      "id":"per",
      "title":"每页显示",
      "choice":[10,15,20,50,100],
      "default":15,
      "onclick":"changePerPage"
    },{
      "id":"asc",
      "title":"排序方式",
      "choice":["---","姓名","班别","年级","志愿点","时段","报名时间","通过状态"],
      "default":"ID",
      "onclick":"sortme"
    },{
      "id":"loc",
      "title":"筛选地点",
      "choice":[ "---", <?php
        $j=json_decode(file_get_contents("../location.json"));
        $j=$j->loc;
        for($i=0;$i<sizeof($j);$i++) {
          echo('"'.$j[$i]->name.'"');
          if($i<sizeof($j)-1) echo ", ";
        }
      ?> ],
      "default":"---",
      "onclick":"floc"
    },{
      "id":"cls",
      "title":"筛选班别",
      "choice":[ <?php
        for($i=0;$i<2;$i++){
          for($j=1;$j<18;$j++){
            echo('"高'.(($i==0)?"一":"二").(($j<10)?('0'.$j):$j).'班"');
            if(($j<18 && $i==0)||($j<17 && $i==1)) echo ", ";
          }
        }
      ?> , '---'],
      "default":"---",
      "onclick":"fclass"
    },{
      "id":"xls",
      "title":"导出Excel",
      "choice":["选中","本页","自动分班"],
      "default":"---",
      "onclick":"exportCSV",
      "ignore":1
    }
  ]
);

/**
* function mkfilters  根据json生成过滤器按钮
* @param which  需要生成的过滤器id，传入数组
*/
function mkfilters(which){
  mks='<center>';
  for(i=0;i<filters.length;i++){
		//判断传入参数中是否含有定义好的filter，如没有则跳过
		has=0;
    for(q=0;q<which.length;q++){
        if(which[q]==filters[i].id) has++;
    }
		if(!has) continue;

    mks += ('<div class="btn-group dropup">' +
            '<button id="' + filters[i].id +'" type="button" class="btn btn-default dropdown-toggle" data-idn="' + i + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> ' +
            filters[i].title + ' <span class="caret"></span></button><ul class="dropdown-menu">');
    for(j=0;j<filters[i].choice.length;j++){
      mks += '<li><a onclick=\"readfilters(this)\">' + filters[i].choice[j] + '</a></li>';
    }
    mks += '</ul></div>&nbsp;';
  }
  $(mks+'</center>').insertAfter("#tbSign");
}

/**
* function readfilters  读取filter的值并操作style
* @param dom  传入this
*/
function readfilters(dom){
	$(".btn-group-active").removeClass("btn-group-active");
	$(dom).addClass("btn-group-active");

	//获取<a>所隶属的btn
	origbtn=$(dom).parent().parent().prev()[0];
	//应用颜色
	if(/*每页显示和导出excel就不用高亮了...*/origbtn.id=='per' || origbtn.id=='xls' || dom.innerText=="---"){
		$("#"+origbtn.id).removeClass("btn-pink");
	} else {
		$("#"+origbtn.id).addClass("btn-pink");
	}
	//运行函数
	eval(filters[origbtn.dataset.idn].onclick + "(\"" + dom.innerText + "\");");
}

/**
* function sortme   排序相关
* @param val  根据val来排序
*/
function sortme(val){
  sortby = (val=='---') ? "" : val ;
  req(1);
}

/**
* function floc   过滤地点相关
* @param val  根据val来过滤地点
*/
function floc(val){
  filtername = (val=='---') ? "" : val ;
  updatePageCount();
}

/**
* function fclass   过滤班级相关
* @param val  根据val来过滤班级
*/
function fclass(val){
  classname = (val=='---') ? "" : val ;
  updatePageCount();
}


</script>