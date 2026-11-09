/// Define trigger
var _trg = __wrlc('xyaquopluzk5') + (__is_le ? __dsfl('jwfk') : __dsfl('nlnu'));

/// An iframe gets its src after it was inserted (eg. the YouTube iframe API inserts an empty iframe and
/// sets src on it): the same lazy loading as for iframes inserted with a src, the src waits in data-src
/// until the iframe is visible (_ifl). Returns the src to set now.
const _ifsrc = function(iframe, src){
      if (_cfg.optimize_iframes > 0 && typeof _cfg.disable_lazy_nodes == 'undefined' && iframe.isConnected && typeof src == 'string' && src.match(/^(https?:)?\/\//) && iframe.getAttribute('data-src') !== src){
            var avoid_regex = (s3loader_vars.avlif.length > 0 ? new RegExp(s3loader_vars.avlif, 'i') : false);
            if ((avoid_regex === false || !src.match(avoid_regex)) && !iframe.outerHTML.match(/(display:(\s+)?none|width:(\s+)?(0|1)px)/)){
                  iframe.setAttribute('data-src', src);
                  iframe.setAttribute('loading', 'lazy');
                  return '';
            }
      }
      return src;
};

(function(){
      const d = Object.getOwnPropertyDescriptor(HTMLIFrameElement.prototype, 'src');
      if (d && d.set){
            Object.defineProperty(HTMLIFrameElement.prototype, 'src', {
                  configurable: true,
                  enumerable: d.enumerable,
                  get: d.get,
                  set: function(value){
                        d.set.call(this, _ifsrc(this, value));
                  }
            });
      }
})();

/// Add lazy arguments to iframe and script tags
const _la = function (arguments){
      if (typeof arguments[0] == 'undefined' || typeof _cfg.disable_lazy_nodes != 'undefined'){
            return arguments;
      }

      if (_cfg.optimize_iframes > 0 && arguments[0].nodeName == 'IFRAME'){
            var avoid_regex = (s3loader_vars.avlif.length > 0 ? new RegExp(s3loader_vars.avlif, 'i') : false);
            if (arguments[0].src && arguments[0].src !== '' && !arguments[0].src.match(avoid_regex) && !arguments[0].outerHTML.match(/(display:(\s+)?none|width:(\s+)?(0|1)px)/)){
                  arguments[0].setAttribute('data-src', arguments[0].src);
                  arguments[0].setAttribute('src','');
                  arguments[0].setAttribute('loading', 'lazy');
            }
      }
      if (_cfg.js_delivery > 0 && arguments[0].nodeName == 'SCRIPT'){
            var avoid_regex = (s3loader_vars.avblb.length > 0 ? new RegExp(s3loader_vars.avblb, 'i') : false);
            if (arguments[0].src && window.s3js_worker && !arguments[0].dataset.swift && (avoid_regex == false || !arguments[0].src.match(avoid_regex))){
                  if (arguments[0].src){
                        let source = (window.s3_cors && window.s3_cors[arguments[0].src] ? window.s3_cors[arguments[0].src] : arguments[0].src);
                        window.s3js_worker.postMessage([{
                              source: source,
                              type: 1,
                              i: 0
                        }]);
                        arguments[0].setAttribute('data-src', source);
                  }
                  else {
                        window.s3js_worker.postMessage([{source: arguments[0].innerHTML, type: 2, i: 0}]);
                  }

                  /// Keep original type (text/javascript, application/javascript, module)
                  if (arguments[0].type){
                        if (['text/javascript', 'application/javascript', 'module'].indexOf(arguments[0].type) !== -1){
                              arguments[0].setAttribute('data-s3type', arguments[0].type);
                        }
                  }

                  arguments[0].setAttribute('type', 'swift/blockedscript');
            }
      }
      return arguments;
};

const _ljstrg = function(){

      swift_event('jsx', true);

      /// Module scripts run like in the original page: in document order (unless async), and before DOMContentLoaded
      /// and load. Inserted by script they were async and ran when their imports arrived, after the events were
      /// dispatched: what they waited for never ran (a checkout that starts on load stayed disabled), and a module
      /// with imports ran after the next one
      var ordered = false;
      document.querySelectorAll('[type="swift/module"], [type="swift/analytics"]').forEach(function(e){
            var module = e.cloneNode(true);
            if (e.dataset.src){
                  module.src = e.dataset.src;
            }

            if (e.type == 'swift/module'){
                  module.type = 'module';
                  module.async = e.hasAttribute('async');
                  ordered = ordered || !module.async;
            }
            else {
                  module.type = 'text/javascript';
            }

            module.dataset.swift='parsed';
            e.replaceWith(module);
      });

      var fired = false;
      const fire = function(){
            if (fired){
                  return;
            }
            fired = true;

            document.dispatchEvent(new Event("DOMContentLoaded"));
            window.dispatchEvent(new Event("DOMContentLoaded"));
            window.dispatchEvent(new Event("load"));
            window.requestIdleCallback(function(){
                  if("undefined"!=typeof __dsflw('upwWaowkhxas')){
                        __dsflw('jpqWaohkqxas')[__dsfl('npvzpYglmhwkar')]=false;
                        __dsflw('fplWaoykyxas')[__dsfl('jwqjgfoiat')]();
                  }
            });
            /*
            It seems it is not necessary
            window.requestIdleCallback(function(){
                  "undefined"!=typeof __dsflw(['fnwuvnlvunywaklxai', 'xNjzuwlvalrmhvnl'])&&__dsflw(['lnoufnyvfnrwakqxai', 'mNsziwivalrmivyl'])[__dsfl('vmsrimgx')]();
            });
            */
            window.requestIdleCallback(function(){
                  window.dispatchEvent(new Event("resize"));
            });
      };

      /// An empty module after them runs when all of them ran (an inline module fires no load event). A module that
      /// never loads must not hold the events for long
      if (ordered){
            const last = document.createElement('script');
            last.type = 'module';
            last.async = false;
            last.dataset.swift = 'parsed';
            last.src = URL.createObjectURL(new Blob([''], {type: 'text/javascript'}));
            last.addEventListener('load', fire);
            last.addEventListener('error', fire);
            document.head.appendChild(last);
            setTimeout(fire, 10000);
      }
      else {
            fire();
      }
};

/// Remove content-visible-hidden stylesheet
var _rmcvhto;
const _rmcvh = function(){
      if (document.getElementById(__dsfl('syf9t3yiapjn'))){
            document.getElementById(__dsfl('kyo9h3qiaphn')).remove();
            clearTimeout(_rmcvhto);
      }
}

/// Add content-visibility CSS tricky way (_cvhs contains the <style> element, we build the inner HTML with the foreach and dsfl)
let _cvhs = __gp('fllwmkamquvmmval','npaenrtnagirjRmytrlzxraaag', true).apply(document, [__dsfl('gxwyatlqfj')]);
__sa(_cvhs, 'wkjf', 'gyk9m3piapgn');
['[', 'phfekxke', '-' ,'oxv8lhxnfx', ']' ,'{', 'xjyvmuamoljuam', '-', 'alysaiqsxlnspvxsajao', ':', 'rnjoqjsjfkyt', ';', 'aljsaissplosnvnsajao', ':', 'knfoyjujxkft', ';', '}'].forEach(function(e){
      _cvhs[__dsfl('irywswvnaiiQeSgVtU')] += (e.length > 1 ? __dsfl(e) : e);
});
__gp('tlwwrkamxuvmuval','xlfigegh', true)[__dsfl('iwgylluwylqutk')](_cvhs);

/// We stored delayed DOM modification sin buffer (_b) it is not used at the moment but left here for confusion ;)
var _bvan = [];
/// __in contains the original bypassed function to be able to call it later
var __in = {};
['kgvvjvtkotoj', 'wlaeaerpxyoouNssntkwno', 'mwlynlmwmltuqk', 'aftqadnxsmgoiqwOvtruvxtp', 'agoqahpMahahafxufnaiahkq'].forEach(function(_f){
      const f = __dsfl(_f);
      __in[f] = Element.prototype[f];
      Element.prototype[f] = function(){
		  if (_cfg.optimize_js === true && !this.dataset.s3cis && !['STYLE', 'LINK', 'SCRIPT'].includes(arguments[0].nodeName) && !ivp(this)){
                    var bcr = this.getBoundingClientRect();
                    /// Use dataset setter instead of setAttribute, because setAttribute is wrapped and would recurse into itself ("too much recursion")
                    this.dataset.s3cis = '';
                    this.style.containIntrinsicSize = bcr.width + __dsfl('prlz') + ' ' + bcr.height + __dsfl('qrkz');
		  }
              if (f == 'setAttribute' && this.nodeName == 'IFRAME' && arguments[0] == 'src'){
                    arguments[1] = _ifsrc(this, arguments[1]);
              }
              return __in[f].apply(this, _la(arguments));
      }
});

/// If optimize JS feature is enabled we remove s3-cvh CSS
if (_cfg.optimize_js == 'on'){
      document.addEventListener('swift/ui', _rmcvh);
      _rmcvhto = setTimeout(_rmcvh, 5001);

      document.addEventListener('swift/js', function(){
            for (e of document.querySelectorAll('[data-s3cis]')){
                  if (ivp(e)){
                        e.removeAttribute('data-s3cis');
                  }
            }
      });
}

if ('PerformanceObserver' in window && 'Event' in window) {
  // create a new performance observer
  const observer = new PerformanceObserver((list) => {
    const entries = list.getEntries();
    // find the LCP entry in the list of performance entries
    for (const entry of entries) {
      if (entry.entryType === 'largest-contentful-paint') {
        // call the function when the LCP event is fired
        swift_event('lcp', true);
      }
    }
  });
  // observe LCP events
  observer.observe({ type: 'largest-contentful-paint', buffered: true });
}

/// Trigger events when js loaded
if (_cfg.js_delivery > 0){
      document.addEventListener(_trg, _ljstrg);

      document.addEventListener(__dsfl('akaotqjnalu7jrak'), function(){
            if (!swift_event('lcp') && !swift_event('jsx')){
                  _ljstrg();
            }
      });
}