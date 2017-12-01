/**
 * Global Function
 * @description 公共函数
 * @author David Ding
 * @copyright 2012-2017 DingStudio All Rights Reserved
 */

function UrlHashRouter() {
  var urlhash = window.location.hash;
  var oldtitle = document.getElementsByTagName('title')[0].innerHTML;
  switch (urlhash) {
    case "#ArticleManager":
      changeTitle('文章管理 -' + oldtitle);
      fillTpl('postmgr');
      getArticleList('0');
      break;
    case "#ClassManager":
      changeTitle('分类管理 -' + oldtitle);
      fillTpl('classmgr');
      break;
    case "#Dashboard":
      changeTitle('信息面板 -' + oldtitle);
      fillTpl('dashboard');
      getArticleList('3');
      break;
    default:
      location.hash = "Dashboard";
      break;
  }
  window.onhashchange = function () {
    var urlhash = window.location.hash;
    switch (urlhash) {
      case "#ArticleManager":
        changeTitle('文章管理 -' + oldtitle);
        fillTpl('postmgr');
        getArticleList('0');
        break;
      case "#ClassManager":
        changeTitle('分类管理 -' + oldtitle);
        fillTpl('classmgr');
        break;
      case "#Dashboard":
        changeTitle('信息面板 -' + oldtitle);
        fillTpl('dashboard');
        getArticleList('3');
        break;
      default:
        location.href = './index.php?c=notfound';
        break;
    }
  }
}

/**
 * 重写局部页面模板填充逻辑
 * @param {string} tplName 模板标识名
 * @return {null} 无返回
 */
function fillTpl(tplName) {
  var xhr;
  if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
  }
  else {
    xhr = new ActiveXObject('Microsoft.XMLHTTP');
  }
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        document.getElementsByClassName('post-body')[0].innerHTML = xhr.responseText;
      }
      else if (xhr.status == 500) {
        alert('抱歉，服务器忙碌或配置存在异常！如果该现象多次出现，请联系站点管理员。');
        return false;
      }
      else {
        alert('发生未知错误，错误代码：' + xhr.status + '。请将此错误代码提供给软件开发者作进一步分析！');
        return false;
      }
    }
  }
  xhr.withCredentials = true;
  xhr.open('POST', './api.php?action=Template', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('Tpl=' + tplName);
}

/**
 * 前台获取首页博客文章列表过程
 * @return {null}
 */
function getArticleIndex() {
  var xhr;
  var xhr;
  if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
  }
  else {
    xhr = new ActiveXObject('Microsoft.XMLHTTP');
  }
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        var data = eval("(" + xhr.responseText + ")");
        if (data.code != 0) {
          alert('文章数据拉取发生异常，请检查数据库系统是否正常。');
          return false;
        }
        else if (data.data == null) {
          alert('您还没有发表过任何文章，将为您自动跳转至用户中心！');
          location.href = './index.php?c=manager';
          return false;
        }
        else {
          var html = '';
          var aid,title,content,ctime;
          var list = document.getElementById('posts');
          data.data.forEach(function(item,index,data) {
            aid = item.aid;
            title = item.title;
            content = item.content.replace(/<(style|script|iframe)[^>]*?>[\s\S]+?<\/\1\s*>/gi,'').replace(/<[^>]+?>/g,'').replace(/\s+/g,' ').replace(/ /g,' ').replace(/>/g,' ').substring(0,100);
            ctime = item.ctime;
            html += '<article class="post post-type-normal " itemscope itemtype="http://schema.org/Article">\
            <header class="post-header"><h1 class="post-title" itemprop="name headline">\
            <a class="post-title-link" href="./index.php?c=detail&aid='+aid+'" itemprop="url">'+title+'</a>\
            </h1><div class="post-meta">\
            <span class="post-time"><span class="post-meta-item-icon"><i class="fa fa-calendar-o"></i></span>\
            <span class="post-meta-item-text">发表于</span>\
            <time itemprop="dateCreated" datetime="'+ctime+'" content="'+ctime+'">'+ctime+'\
            </time></span></div></header><div class="post-body" itemprop="articleBody">文章概要：'+content+'</div>\
            <div class="post-more-link text-center"><a class="btn" href="./index.php?c=detail&aid='+aid+'" rel="contents">\
            阅读全文 &raquo;</a></div><div></div><footer class="post-footer"><div class="post-eof"></div></footer></article>';
          });
          list.innerHTML = html;
        }
      }
      else {
        alert('系统异常！请与管理员联系。');
        return false;
      }
    }
  }
  xhr.withCredentials = true;
  xhr.open('POST', './api.php?action=getArticle', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('limit=0&type=full');
}

/**
 * 前台获取首页博客完整文章过程
 * @return {null}
 */
function getArticleDetail() {
  var xhr;
  var xhr;
  if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
  }
  else {
    xhr = new ActiveXObject('Microsoft.XMLHTTP');
  }
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        var data = eval("(" + xhr.responseText + ")");
        if (data.code != 0) {
          alert('文章数据拉取发生异常，请检查数据库系统是否正常。');
          return false;
        }
        else if (data.data == null) {
          alert('抱歉，该文章可能已经被删除。即将返回首页！');
          location.href = './index.php';
          return false;
        }
        else {
          var html = '';
          var aid,title,content,ctime;
          var list = document.getElementById('posts');
          data.data.forEach(function(item,index,data) {
            aid = item.aid;
            title = item.title;
            content = item.content;
            ctime = item.ctime;
            html += '<article class="post post-type-normal " itemscope itemtype="http://schema.org/Article">\
            <header class="post-header"><h1 class="post-title" itemprop="name headline">\
            <a class="post-title-link" href="./index.php?c=detail&aid='+aid+'" itemprop="url">'+title+'</a>\
            </h1><div class="post-meta">\
            <span class="post-time"><span class="post-meta-item-icon"><i class="fa fa-calendar-o"></i></span>\
            <span class="post-meta-item-text">发表于</span>\
            <time itemprop="dateCreated" datetime="'+ctime+'" content="'+ctime+'">'+ctime+'\
            </time></span></div></header><div class="post-body" itemprop="articleBody">'+content+'</div>\
            <div class="post-more-link text-center"><a class="btn" href="javascript:void(0);" onclick="alert(\'评论系统尚未开发完毕，敬请期待！\')" rel="contents">\
            展开评论 &raquo;</a></div><div></div><footer class="post-footer"><div class="post-eof"></div></footer></article><div class="post-spread"></div>';
          });
          changeTitle(title);
          list.innerHTML = html;
        }
      }
      else {
        alert('系统异常！请与管理员联系。');
        return false;
      }
    }
  }
  xhr.withCredentials = true;
  xhr.open('POST', './api.php?action=getArticle', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('limit=0&type=full&aid=' + getUrlParam('aid'));
}

/**
 * 信息面板数据的拉取
 * @param {string} limit_count 输出限制数
 * @return {null}
 */
function getArticleList(limit_count) {
  var xhr;
  if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
  }
  else {
    xhr = new ActiveXObject('Microsoft.XMLHTTP');
  }
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        var data = eval("(" + xhr.responseText + ")");
        if (data.code != 0) {
          alert('文章数据拉取发生异常，可能是由于您的登录会话已过期所致。');
          return false;
        }
        else if (data.data == null) {
          alert('看起来您还没有发表文章，快新建一篇文章吧。');
          return false;
        }
        else {
          var aid;
          var title;
          var table = '<tr><th bgcolor="#EBEBEB">文章标题（点击编辑）</th><th bgcolor="#EBEBEB">更新时间</th><th bgcolor="#EBEBEB">管理操作</th></tr>';
          data.data.forEach(function(item,index,data) {
            aid = item.aid;
            title = item.title;
            ctime = item.ctime;
            table += '\
            <tr>\
            <td align="left" bgcolor="#FFFFFF"><font color="MediumSeaGreen" onclick="openMiniWindow(\'./post.php?aid=' + aid + '\',800,600)">' + title + '</font></td>\
            <td align="left" bgcolor="#FFFFFF"><font color="MediumSeaGreen">' + ctime + '</font></td>\
            <td align="center" bgcolor="#FFFFFF"><font color="OrangeRed" onclick="delArticle(' + aid + ')">删除</font></tr>';
          });
          document.getElementById('siteInformation').innerHTML = table;
        }
      }
      else if (xhr.status == 500) {
        alert('抱歉，服务器忙碌或配置存在异常！如果该现象多次出现，请联系站点管理员。');
        return false;
      }
      else {
        console.log('XmlHttp request exception, status code：' + xhr.status + '. Please contact application developer!');
        return false;
      }
    }
  }
  xhr.withCredentials = true;
  xhr.open('POST', './api.php?action=getArticle', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('limit=' + limit_count + '&type=list');
}

/**
 * 博客文章的删除过程
 * @param {integer} 文章ID
 * @return {null}
 */
function delArticle(aid) {
  if (typeof (aid) == 'undefined' && aid == '') {
    alert('由于该页面过期，无法继续操作。稍后为您刷新！');
    location.reload();
    return false;
  }
  var xhr;
  if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
  }
  else {
    xhr = new ActiveXObject('Microsoft.XMLHTTP');
  }
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        var data = eval("(" + xhr.responseText + ")");
        if (data.code == 0) {
          console.log(data.message);
          alert('删除成功！即将为您刷新页面。');
          location.reload();
        }
        else {
          console.log(data.message);
          alert('删除失败，可能是数据中心忙碌或您的数据库空间处于只读状态！请核查这些问题后再次尝试。');
          return false;
        }
      }
      else {
        alert('发生未知错误，错误代码：' + xhr.status + '。请将此错误代码提供给软件开发者作进一步分析！');
        return false;
      }
    }
  }
  xhr.withCredentials = true;
  xhr.open('POST', './api.php?action=post/remove', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('aid=' + aid);
}

/**
 * 创建新的弹出窗口（带callback监控刷新）
 * @param {string} url url地址
 * @param {integer} w 窗体宽度
 * @param {integer} h 窗体高度
 * @return {null}
 */
function openMiniWindow(url,w=800,h=600) {
  var my_window = window.open(url,'edit_post','width=' + w + ',height=' + h);
  var my_window_timer = setInterval(function() {
    if (my_window.closed) {
      clearInterval(my_window_timer);
      location.reload();
    }
  },1000);
}

/**
 * 快速修改网页Title标签
 * @param {string} text 传入网页标题字符串
 * @return {boolean} 返回T/F布尔值
 */
function changeTitle(text) {
  if (typeof (text) == 'undefined' && text == '') {
    alert('标题更改失败，传入了无效的数据格式！');
    return false;
  }
  else {
    document.getElementsByTagName('title')[0].innerHTML = text;
    return true;
  }
}

/**
 * 获取Url参数
 * @param string key url参数名
 */
function getUrlParam(key) {
  var search = window.location.search;
  if (!search) {
    return '';
  }
  var reg = new RegExp('.*' + key + '=([^&]*)' + '.*');
  return decodeURIComponent(search.replace(reg, '$1') || '');
}

/**
 * 异步登录过程
 */
function postLogin() {
  var user = document.getElementById('username').value;
  var pswd = document.getElementById('password').value;
  if (user == '' || pswd == '') {
    alert('用户名或密码不能为空，请重试。');
    return false;
  }
  var json = '{"uname":"' + user + '","upswd":"' + MD5(pswd) + '"}';
  //console.log(json);
  var xhr;
  if (window.XMLHttpRequest) {
    xhr = new XMLHttpRequest();
  }
  else {
    xhr = new ActiveXObject('Microsoft.XMLHTTP');
  }
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4) {
      if (xhr.status == 200) {
        var data = eval("(" + xhr.responseText + ")");
        if (data.code == 0) {
          console.log(data.message);
          alert('登录成功！即将为您刷新页面。');
          if (getUrlParam('callback') == '') {
            location.href = './index.php?c=index';
          }
          else if (getUrlParam('callback') == 'miniform') {
            window.close();
          }
          else {
            location.href = decodeURI(getUrlParam('callback'));
          }
        }
        else if (data.code == -8) {
          console.log(data.message);
          alert('抱歉，服务器忙碌。暂时无法处理您的登录请求，请稍后再次尝试。');
          return false;
        }
        else if (data.code == -1) {
          console.log(data.message);
          alert('用户名或密码不正确，请重试。');
          return false;
        }
        else if (data.code == -3 || data.code == -5) {
          console.log(data.message);
          alert('服务器不可接受的数据类型，可能是由于您的浏览器编码兼容问题所致！建议更换最新的浏览器再次尝试。');
          return false;
        }
        else {
          console.log(data.message);
          alert('数据写入失败，可能是数据中心忙碌或您的数据库空间处于只读状态！请核查这些问题后再次尝试。');
          return false;
        }
      }
      else if (xhr.status == 500) {
        alert('抱歉，服务器忙碌或配置存在异常！如果该现象多次出现，请联系站点管理员。');
        return false;
      }
      else {
        alert('发生未知错误，错误代码：' + xhr.status + '。请将此错误代码提供给软件开发者作进一步分析！');
        return false;
      }
    }
  }
  xhr.withCredentials = true;
  xhr.open('POST', './api.php?action=auth/login', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('data=' + json);
}
