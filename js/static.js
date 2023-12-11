const form = document.querySelector("form");
const rootfolder = document.querySelector("#folderNfile");
const rootfolderdl = document.querySelector("#folderNfile dl");
const rootsubfolder = document.querySelector("#subfolderNfile");

form.addEventListener("change", function () {
  //class du plugin
  let plugin = document.querySelector("input#title").value;
  let classPlugin = plugin.replace(/(<([^>]+)>)/ig, "");
  classPlugin = classPlugin.replace(/[^a-zA-Z]/g, "");
  classPlugin = classPlugin.replace(/\s/g, "");
  rootfolder.innerHTML = "";
  rootsubfolder.innerHTML = "";
  let rootName = document.createElement("legend");
  rootName.textContent = classPlugin+'.zip';
  rootfolder.append(rootName);
  let subrootName = document.createElement("legend");
  subrootName.textContent = SUBFOLDER +' '+ classPlugin;
  rootsubfolder.append(subrootName);
  let rootfile= document.createElement("dl");
  let subrootfile= document.createElement("dl");
  let dtitle = document.createElement('dt');
  dtitle.textContent=classPlugin;
  rootfile.append(dtitle);
  console.clear();
  let plugIcon = document.querySelector("input#icone").value;
  let root = [ classPlugin+'.php','infos.xml'];
  let subRoot = ["lang","img","assets"];
  let lang = ["fr.php", "fr-help.php"];
  let css = [];
  let js = [];

  console.log(classPlugin);
  if (plugIcon.length > 0)  root.push("icon.png");
  if (document.querySelector("#config:checked")) {
    root.push("config.php");
    css.push("admin.css");
    js.push("admin.js");
    subRoot.push("css");
    subRoot.push("js");
  }
  if (document.querySelector("#admin:checked")) {
    root.push("admin.php");
    css.push("admin.css");
    js.push("admin.js");
    subRoot.push("css");
    subRoot.push("js");
  }
  if (document.querySelector("#static:checked")) {
    root.push("static." + classPlugin + ".php");
    css.push("site.css");
    js.push("site.js");
  }
  if (document.querySelector("#addWidget:checked")) {
    root.push("widget." + classPlugin + ".php");
    css.push("site.css");
    js.push("site.js");
    subRoot.push("css");
    subRoot.push("js");
  }
  if (document.querySelector("#wizard:checked")) {
    lang.push(LANG+"-wizard.php");
    css.push("wizard.css");
    js.push("wizard.js");
    subRoot.push("css");
    subRoot.push("js");
  }
  subRoot = [...new Set(subRoot)];
  css = [...new Set(css)];
  js = [...new Set(js)];
  
 
  subRoot.forEach(function (item, index, array) {
  let subdd =  document.createElement('dd');
  let subdtitle = document.createElement('dt');
    subdd.textContent=item;
    subdd.setAttribute('class','subfolder')
    rootfile.append(subdd) ;
    subdtitle.textContent=item;
    subdtitle.setAttribute('id',item)
    subrootfile.append(subdtitle)      
  });
    rootsubfolder.append(subrootfile)
	
  root.forEach(function (item, index, array) {
    let ddetails = document.createElement('dd');
    ddetails.textContent=item;
    rootfile.append(ddetails)    
  });
  rootfolder.append(rootfile);
  

  
  css.forEach(function (item, index, array) {
    let subfile=document.createElement('dd');
    subfile.textContent=item;
    let ext = item.split('.').pop();
    let sib = rootsubfolder.querySelector('#'+ext);
    sib.after(subfile);
  });

  lang.forEach(function (item, index, array) {
    let subfile=document.createElement('dd');
     subfile.textContent=item;
    let sib = rootsubfolder.querySelector('#lang');
    sib.after(subfile);
  });
  
  js.forEach(function (item, index, array) {
    let subfile=document.createElement('dd');
    subfile.textContent=item;
    let ext = item.split('.').pop();
    let sib = rootsubfolder.querySelector('#'+ext);
    sib.after(subfile);
  });
});

