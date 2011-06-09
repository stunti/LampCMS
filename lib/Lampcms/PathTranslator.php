<?php
$arr = array(
        "^/questions([\/]{0,1})$" => "/index.php?a=viewquestions",
        "^/questions/page([0-9]+)\.html" => "/index.php?a=viewquestions&pageID=$1",
        "^/questions/([0-9]+)/(.*)" => "/index.php?a=viewquestion&qid=$1&urltext=$2",
        "^/q([0-9]+)/([^\/]+)/([a-zA-Z_]+)/page([0-9]+)\.html" => "/index.php?a=viewquestion&qid=$1&urltext=$2&sort=$3&pageID=$4",
        "^/q([0-9]+)/(.*)" => "/index.php?a=viewquestion&qid=$1&urltext=$2",
        "^/ask([\/]{0,1})$" => "/index.php?a=askform",
        "^/voted([\/]{0,1})$" => "/index.php?a=viewquestions&cond=voted",
        "^/voted/page([0-9]+)\.htm([l]{0,1})$" => "/index.php?a=viewquestions&cond=voted&pageID=$1",
        "^/active([\/]{0,1})$" => "/index.php?a=viewquestions&cond=active",
        "^/active/page([0-9]+)\.htm([l]{0,1})$" => "/index.php?a=viewquestions&cond=active&pageID=$1",
        "^/unanswered([\/]{0,1})$" => "/index.php?a=unanswered",
        "^/unanswered/noanswers([\/]{0,1})$" => "/index.php?a=unanswered&cond=noanswer",
        "^/unanswered/noanswers/page([0-9]+)\.html$" => "/index.php?a=unanswered&cond=noanswer&pageID=$1",
        "^/unanswered/tagged/([^\/]*)([\/]{0,1})$" => "/index.php?a=unanswered&cond=tagged&tags=$1",
        "^/unanswered/tagged/([^\/]*)/page([0-9]+)\.html$" => "/index.php?a=unanswered&cond=tagged&tags=$1&pageID=$2",
        "^/unanswered/page([0-9]+)\.html$" => "/index.php?a=unanswered&pageID=$1",
        "^/tags([\/]{0,1})$" => "/index.php?a=viewqtags",
        "^/tags/page([0-9]+)\.htm([l]{0,1})$" => "/index.php?a=viewqtags&pageID=$1",
        "^/tags/name([\/]{0,1})$" => "/index.php?a=viewqtags&cond=name",
        "^/tags/recent([\/]{0,1})$" => "/index.php?a=viewqtags&cond=recent",
        "^/tags/popular([\/]{0,1})$" => "/index.php?a=viewqtags&cond=popular",
        #
        "^/tags/(name|recent|popular)/page([0-9]+)\.html$" => "/index.php?a=viewqtags&cond=$1&pageID=$2",
        #
        "^/tagged/(.*)/$" => "/index.php?a=tagged&tags=$1",
        "^/tagged/(.*)/page([0-9]+)\.html$" => "/index.php?a=tagged&tags=$1&pageID=$2",
        "^/vote/([0-9]+)/(up|down)$" => "/index.php?a=vote&resid=$1&res=q&type=$2",
        "^/ansvote/([0-9]+)/(up|down)$" => "/index.php?a=vote&resid=$1&res=a&type=$2",
        "^/accept/([0-9]+)$" => "/index.php?a=accept&aid=$1",
        "^/users/([0-9]+)/(.*)" => "/index.php?a=userinfo&uid=$1&username=$2",
        "^/users/([a-zA-Z]+)/page([0-9]+)\.html" => "/index.php?a=users&sort=$1&pageID=$2",
        "^/users/([a-zA-Z]+)/" => "/index.php?a=users&sort=$1",
        #
        #       
        "/register" => "/index.php?a=register",
        "^/([a-zA-Z\-]+)/page([0-9]+)\.html$" => "/index.php?a=$1&pageID=$2",
        "^/aa/([0-9]+)/([a-f0-9]+)$" => "/index.php?a=activate&eid=$1&hash=$2",
        "^/([a-zA-Z\-]+)/$" => "/index.php?a=$1",
        "^/search/(m|r)/(.*)/page([0-9]+)\.html$" => "/index.php?a=search&ord=$1&q=$2&pageID=$3",
        "^/tab/(a|q)/([0-9]+)/([a-zA-Z]+)/page([0-9]+)\.html$" => "/index.php?a=userinfotab&tab=$1&uid=$2&sort=$3&pageID=$4",
        "^/editprofile/([0-9]+)" => "/index.php?a=editprofile&uid=$1"
);
foreach($arr as $regex => $target) {
	$res = preg_replace('#'.$regex.'#iu', $target, $_SERVER['REQUEST_URI']);
	if ($res !== $_SERVER['REQUEST_URI']) {
		$url = str_replace('/index.php?','', $res);
		parse_str($url, $res);
		$_GET = $_GET + $res;
		break;
	}
}

