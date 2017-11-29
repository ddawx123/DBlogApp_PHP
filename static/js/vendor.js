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
      break;
    case "#ClassManager":
      changeTitle('分类管理 -' + oldtitle);
      fillTpl('classmgr');
      break;
    case "#Dashboard":
      changeTitle('信息面板 -' + oldtitle);
      fillTpl('dashboard');
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
          var table = '<tr><th bgcolor="#EBEBEB">文章ID</th><th bgcolor="#EBEBEB">文章标题</th></tr>';
          data.data.forEach(function(item,index,data) {
            aid = item.aid;
            title = item.title;
            table += '\
            <tr><td align="left" bgcolor="#FFFFFF"><font color="MediumSeaGreen">' + aid + '</font></td><td align="left" bgcolor="#FFFFFF"><font color="MediumSeaGreen">' + title + '</font></td></tr>';
          });
          document.getElementById('siteInformation').innerHTML = table;
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
  xhr.open('POST', './api.php?action=getArticle', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send('limit=' + limit_count);
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
