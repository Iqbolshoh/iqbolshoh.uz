const puppeteer = require('/root/dev/algor.uz/node_modules/puppeteer-core');
(async () => {
  const b = await puppeteer.launch({executablePath:'/root/.cache/puppeteer/chrome/linux-151.0.7922.71/chrome-linux64/chrome', args:['--no-sandbox','--allow-file-access-from-files']});
  const p = await b.newPage(); await p.setViewport({width:1920,height:1080});
  await p.goto('file://'+__dirname+'/deck.html',{waitUntil:'networkidle0'});
  await p.evaluate(() => document.fonts.ready);
  const n = await p.$$eval('.slide', s => s.length);
  const els = await p.$$('.slide');
  for (let i=0;i<els.length;i++) await els[i].screenshot({path:`${__dirname}/s${i+1}.png`});
  await p.pdf({path:__dirname+'/portfolio.pdf', width:'1920px', height:'1080px', printBackground:true, pageRanges:''});
  console.log('slides', n);
  await b.close();
})();
