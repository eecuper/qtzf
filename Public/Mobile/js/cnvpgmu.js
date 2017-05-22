var gmu=gmu||{version:'@version',$:window.Zepto,staticCall:(function($){var proto=$.fn,slice=[].slice,instance=$();instance.length=1;return function(item,fn){instance[0]=item;return proto[fn].apply(instance,slice.call(arguments,2))}})(Zepto)};(function(gmu,$){var slice=[].slice,separator=/\s+/,returnFalse=function(){return false},returnTrue=function(){return true};function eachEvent(events,callback,iterator){(events||'').split(separator).forEach(function(type){iterator(type,callback)})};function matcherFor(ns){return new RegExp('(?:^| )'+ns.replace(' ',' .* ?')+'(?: |$)')};function parse(name){var parts=(''+name).split('.');return{e:parts[0],ns:parts.slice(1).sort().join(' ')}};function findHandlers(arr,name,callback,context){var matcher,obj;obj=parse(name);obj.ns&&(matcher=matcherFor(obj.ns));return arr.filter(function(handler){return handler&&(!obj.e||handler.e===obj.e)&&(!obj.ns||matcher.test(handler.ns))&&(!callback||handler.cb===callback||handler.cb._cb===callback)&&(!context||handler.ctx===context)})};function Event(type,props){if(!(this instanceof Event)){return new Event(type,props)};props&&$.extend(this,props);this.type=type;return this};Event.prototype={isDefaultPrevented:returnFalse,isPropagationStopped:returnFalse,preventDefault:function(){this.isDefaultPrevented=returnTrue},stopPropagation:function(){this.isPropagationStopped=returnTrue}};gmu.event={on:function(name,callback,context){var me=this,set;if(!callback){return this};set=this._events||(this._events=[]);eachEvent(name,callback,function(name,callback){var handler=parse(name);handler.cb=callback;handler.ctx=context;handler.ctx2=context||me;handler.id=set.length;set.push(handler)});return this},one:function(name,callback,context){var me=this;if(!callback){return this};eachEvent(name,callback,function(name,callback){var once=function(){me.off(name,once);return callback.apply(context||me,arguments)};once._cb=callback;me.on(name,once,context)});return this},off:function(name,callback,context){var events=this._events;if(!events){return this};if(!name&&!callback&&!context){this._events=[];return this};eachEvent(name,callback,function(name,callback){findHandlers(events,name,callback,context).forEach(function(handler){delete events[handler.id]})});return this},trigger:function(evt){var i=-1,args,events,stoped,len,ev;if(!this._events||!evt){return this};typeof evt==='string'&&(evt=new Event(evt));args=slice.call(arguments,1);evt.args=args;args.unshift(evt);events=findHandlers(this._events,evt.type);if(events){len=events.length;while(++i<len){if((stoped=evt.isPropagationStopped())||false===(ev=events[i]).cb.apply(ev.ctx2,args)){stoped||(evt.stopPropagation(),evt.preventDefault());break}}};return this}};gmu.Event=Event})(gmu,gmu.$);(function(gmu,$,undefined){var slice=[].slice,toString=Object.prototype.toString,blankFn=function(){},staticlist=['options','template','tpl2html'],record=(function(){var data={},id=0,ikey='_gid';return function(obj,key,val){var dkey=obj[ikey]||(obj[ikey]=++id),store=data[dkey]||(data[dkey]={});val!==undefined&&(store[key]=val);val===null&&delete store[key];return store[key]}})(),event=gmu.event;function isPlainObject(obj){return toString.call(obj)==='[object Object]'};function eachObject(obj,iterator){obj&&Object.keys(obj).forEach(function(key){iterator(key,obj[key])})};function parseData(data){try{data=data==='true'?true:data==='false'?false:data==='null'?null:+data+''===data?+data:/(?:\{[\s\S]*\}|\[[\s\S]*\])$/.test(data)?JSON.parse(data):data}catch(ex){data=undefined};return data};function getDomOptions(el){var ret={},attrs=el&&el.attributes,len=attrs&&attrs.length,key,data;while(len--){data=attrs[len];key=data.name;if(key.substring(0,5)!=='data-'){continue};key=key.substring(5);data=parseData(data.value);data===undefined||(ret[key]=data)};return ret};function zeptolize(name){var key=name.substring(0,1).toLowerCase()+name.substring(1),old=$.fn[key];$.fn[key]=function(opts){var args=slice.call(arguments,1),method=typeof opts==='string'&&opts,ret,obj;$.each(this,function(i,el){obj=record(el,name)||new gmu[name](el,isPlainObject(opts)?opts:undefined);if(method==='this'){ret=obj;return false}else if(method){if(!$.isFunction(obj[method])){throw new Error('组件没有此方法：'+method)};ret=obj[method].apply(obj,args);if(ret!==undefined&&ret!==obj){return false};ret=undefined}});return ret!==undefined?ret:this};$.fn[key].noConflict=function(){$.fn[key]=old;return this}};function loadOption(klass,opts){var me=this;if(klass.superClass){loadOption.call(me,klass.superClass,opts)};eachObject(record(klass,'options'),function(key,option){option.forEach(function(item){var condition=item[0],fn=item[1];if(condition==='*'||($.isFunction(condition)&&condition.call(me,opts[key]))||condition===opts[key]){fn.call(me)}})})};function loadPlugins(klass,opts){var me=this;if(klass.superClass){loadPlugins.call(me,klass.superClass,opts)};eachObject(record(klass,'plugins'),function(opt,plugin){if(opts[opt]===false){return};eachObject(plugin,function(key,val){var oringFn;if($.isFunction(val)&&(oringFn=me[key])){me[key]=function(){var origin=me.origin,ret;me.origin=oringFn;ret=val.apply(me,arguments);origin===undefined?delete me.origin:(me.origin=origin);return ret}}else{me[key]=val}});plugin._init.call(me)})};function mergeObj(){var args=slice.call(arguments),i=args.length,last;while(i--){last=last||args[i];isPlainObject(args[i])||args.splice(i,1)};return args.length?$.extend.apply(null,[true,{}].concat(args)):last};function bootstrap(name,klass,uid,el,options){var me=this,opts;if(isPlainObject(el)){options=el;el=undefined};options&&options.el&&(el=$(options.el));el&&(me.$el=$(el),el=me.$el[0]);opts=me._options=mergeObj(klass.options,getDomOptions(el),options);me.template=mergeObj(klass.template,opts.template);me.tpl2html=mergeObj(klass.tpl2html,opts.tpl2html);me.widgetName=name.toLowerCase();me.eventNs='.'+me.widgetName+uid;me._init(opts);me._options.setup=(me.$el&&me.$el.parent()[0])?true:false;loadOption.call(me,klass,opts);loadPlugins.call(me,klass,opts);me._create();me.trigger('ready');el&&record(el,name,me)&&me.on('destroy',function(){record(el,name,null)});return me};function createClass(name,object,superClass){if(typeof superClass!=='function'){superClass=gmu.Base};var uuid=1,suid=1;function klass(el,options){if(name==='Base'){throw new Error('Base类不能直接实例化')};if(!(this instanceof klass)){return new klass(el,options)};return bootstrap.call(this,name,klass,uuid++,el,options)};$.extend(klass,{register:function(name,obj){var plugins=record(klass,'plugins')||record(klass,'plugins',{});obj._init=obj._init||blankFn;plugins[name]=obj;return klass},option:function(option,value,method){var options=record(klass,'options')||record(klass,'options',{});options[option]||(options[option]=[]);options[option].push([value,method]);return klass},inherits:function(obj){return createClass(name+'Sub'+suid++,obj,klass)},extend:function(obj){var proto=klass.prototype,superProto=superClass.prototype;staticlist.forEach(function(item){obj[item]=mergeObj(superClass[item],obj[item]);obj[item]&&(klass[item]=obj[item]);delete obj[item]});eachObject(obj,function(key,val){if(typeof val==='function'&&superProto[key]){proto[key]=function(){var $super=this.$super,ret;this.$super=function(){var args=slice.call(arguments,1);return superProto[key].apply(this,args)};ret=val.apply(this,arguments);$super===undefined?(delete this.$super):(this.$super=$super);return ret}}else{proto[key]=val}})}});klass.superClass=superClass;klass.prototype=Object.create(superClass.prototype);klass.extend(object);return klass};gmu.define=function(name,object,superClass){gmu[name]=createClass(name,object,superClass);zeptolize(name)};gmu.isWidget=function(obj,klass){klass=typeof klass==='string'?gmu[klass]||blankFn:klass;klass=klass||gmu.Base;return obj instanceof klass};gmu.Base=createClass('Base',{_init:blankFn,_create:blankFn,getEl:function(){return this.$el},on:event.on,one:event.one,off:event.off,trigger:function(name){var evt=typeof name==='string'?new gmu.Event(name):name,args=[evt].concat(slice.call(arguments,1)),opEvent=this._options[evt.type],$el=this.getEl();if(opEvent&&$.isFunction(opEvent)){false===opEvent.apply(this,args)&&(evt.stopPropagation(),evt.preventDefault())};event.trigger.apply(this,args);$el&&$el.triggerHandler(evt,(args.shift(),args));return this},tpl2html:function(subpart,data){var tpl=this.template;tpl=typeof subpart==='string'?tpl[subpart]:((data=subpart),tpl);return data||~tpl.indexOf('<%')?$.parseTpl(tpl,data):tpl},destroy:function(){this.$el&&this.$el.off(this.eventNs);this.trigger('destroy');this.off();this.destroyed=true}},Object);$.ui=gmu})(gmu,gmu.$);(function(gmu,$,undefined){var cssPrefix=$.fx.cssPrefix,transitionEnd=$.fx.transitionEnd,translateZ=' translateZ(0)';gmu.define('Slider',{options:{loop:false,speed:400,index:0,selector:{container:'.ui-slider-group'}},template:{item:'<div class="ui-slider-item"><a href="<%= href %>">'+'<img src="<%= pic %>" alt="" /></a>'+'<% if( title ) { %><p><%= title %></p><% } %>'+'</div>'},_create:function(){var me=this,$el=me.getEl(),opts=me._options;me.index=opts.index;me._initDom($el,opts);me._initWidth($el,me.index);me._container.on(transitionEnd+me.eventNs,$.proxy(me._tansitionEnd,me));$(window).bind('resize',function(){me._initWidth($el,me.index)})},_initDom:function($el,opts){var selector=opts.selector,viewNum=opts.viewNum||1,items,container;container=$el.find(selector.container);if(!container.length){container=$('<div></div>');if(!opts.content){if($el.is('ul')){this.$el=container.insertAfter($el);container=$el;$el=this.$el}else{container.append($el.children())}}else{this._createItems(container,opts.content)};container.appendTo($el)};if((items=container.children()).length<viewNum+1){opts.loop=false}while(opts.loop&&container.children().length<3*viewNum){container.append(items.clone())};this.length=container.children().length;this._items=(this._container=container).addClass('ui-slider-group').children().addClass('ui-slider-item').toArray();this.trigger('done.dom',$el.addClass('ui-slider'),opts)},_createItems:function(container,items){var i=0,len=items.length;for(;i<len;i++){container.append(this.tpl2html('item',items[i]))}},_initWidth:function($el,index,force){var me=this,width;width=$(window).width();me.width=width;me._arrange(width,index);me.height=$el.height();me.trigger('width.change')},_arrange:function(width,index){var items=this._items,i=0,item,len;this._slidePos=new Array(items.length);for(len=items.length;i<len;i++){item=items[i];item.style.cssText+='width:'+width+'px;'+'left:'+(i*-width)+'px;';item.setAttribute('data-index',i);this._move(i,i<index?-width:i>index?width:0,0)};this._container.css('width',width*len)},_move:function(index,dist,speed,immediate){var slidePos=this._slidePos,items=this._items;if(slidePos[index]===dist||!items[index]){return};this._translate(index,dist,speed);slidePos[index]=dist;immediate&&items[index].clientLeft},_translate:function(index,dist,speed){var slide=this._items[index],style=slide&&slide.style;if(!style){return false};style.cssText+=cssPrefix+'transition-duration:'+speed+'ms;'+cssPrefix+'transform: translate('+dist+'px, 0)'+translateZ+';'},_circle:function(index,arr){var len;arr=arr||this._items;len=arr.length;return(index%len+len)%arr.length},_tansitionEnd:function(e){if(~~e.target.getAttribute('data-index')!==this.index){return};this.trigger('slideend',this.index)},_slide:function(from,diff,dir,width,speed,opts){var me=this,to;to=me._circle(from-dir*diff);if(!opts.loop){dir=Math.abs(from-to)/(from-to)};this._move(to,-dir*width,0,true);this._move(from,width*dir,speed);this._move(to,0,speed);this.index=to;return this.trigger('slide',to,from)},slideTo:function(to,speed){if(this.index===to||this.index===this._circle(to)){return this};var opts=this._options,index=this.index,diff=Math.abs(index-to),dir=diff/(index-to),width=this.width;speed=speed||opts.speed;return this._slide(index,diff,dir,width,speed,opts)},prev:function(){if(this._options.loop||this.index>0){this.slideTo(this.index-1)};return this},next:function(){if(this._options.loop||this.index+1<this.length){this.slideTo(this.index+1)};return this},getIndex:function(){return this.index},destroy:function(){this._container.off(this.eventNs);$(window).off('ortchange'+this.eventNs);return this.$super('destroy')}})})(gmu,gmu.$);(function(gmu,$,undefined){var map={touchstart:'_onStart',touchmove:'_onMove',touchend:'_onEnd',touchcancel:'_onEnd',click:'_onClick'},isScrolling,start,delta,moved;$.extend(gmu.Slider.options,{stopPropagation:false,disableScroll:false});gmu.Slider.register('touch',{_init:function(){var me=this,$el=me.getEl();me._handler=function(e){me._options.stopPropagation&&e.stopPropagation();return map[e.type]&&me[map[e.type]].call(me,e)};me.on('ready',function(){$el.on('touchstart'+me.eventNs,me._handler);me._container.on('click'+me.eventNs,me._handler)})},_onClick:function(){return!moved},_onStart:function(e){if(e.touches.length>1){return false};var me=this,touche=e.touches[0],opts=me._options,eventNs=me.eventNs,num;start={x:touche.pageX,y:touche.pageY,time:+new Date()};delta={};moved=false;isScrolling=undefined;num=opts.viewNum||1;me._move(opts.loop?me._circle(me.index-num):me.index-num,-me.width,0,true);me._move(opts.loop?me._circle(me.index+num):me.index+num,me.width,0,true);me.$el.on('touchmove'+eventNs+' touchend'+eventNs+' touchcancel'+eventNs,me._handler)},_onMove:function(e){if(e.touches.length>1||e.scale&&e.scale!==1){return false};var opts=this._options,viewNum=opts.viewNum||1,touche=e.touches[0],index=this.index,i,len,pos,slidePos;opts.disableScroll&&e.preventDefault();delta.x=touche.pageX-start.x;delta.y=touche.pageY-start.y;if(typeof isScrolling==='undefined'){isScrolling=Math.abs(delta.x)<Math.abs(delta.y)};if(!isScrolling){e.preventDefault();if(!opts.loop){delta.x/=(!index&&delta.x>0||index===this._items.length-1&&delta.x<0)?(Math.abs(delta.x)/this.width+1):1};slidePos=this._slidePos;for(i=index-viewNum,len=index+2*viewNum;i<len;i++){pos=opts.loop?this._circle(i):i;this._translate(pos,delta.x+slidePos[pos],0)};moved=true}},_onEnd:function(){this.$el.off('touchmove'+this.eventNs+' touchend'+this.eventNs+' touchcancel'+this.eventNs,this._handler);if(!moved){return};var me=this,opts=me._options,viewNum=opts.viewNum||1,index=me.index,slidePos=me._slidePos,duration=+new Date()-start.time,absDeltaX=Math.abs(delta.x),isPastBounds=!opts.loop&&(!index&&delta.x>0||index===slidePos.length-viewNum&&delta.x<0),dir=delta.x>0?1:-1,speed,diff,i,len,pos;if(duration<250){speed=absDeltaX/duration;diff=Math.min(Math.round(speed*viewNum*1.2),viewNum)}else{diff=Math.round(absDeltaX/(me.perWidth||me.width))};if(diff&&!isPastBounds){me._slide(index,diff,dir,me.width,opts.speed,opts,true);if(viewNum>1&&duration>=250&&Math.ceil(absDeltaX/me.perWidth)!==diff){me.index<index?me._move(me.index-1,-me.perWidth,opts.speed):me._move(me.index+viewNum,me.width,opts.speed)}}else{for(i=index-viewNum,len=index+2*viewNum;i<len;i++){pos=opts.loop?me._circle(i):i;me._translate(pos,slidePos[pos],opts.speed)}}}})})(gmu,gmu.$);(function(gmu,$){$.extend(true,gmu.Slider,{options:{autoPlay:true,interval:4000}});gmu.Slider.register('autoplay',{_init:function(){var me=this;me.on('slideend ready',me.resume).on('destory',me.stop);me.getEl().on('touchstart'+me.eventNs,$.proxy(me.stop,me)).on('touchend'+me.eventNs,$.proxy(me.resume,me))},resume:function(){var me=this,opts=me._options;if(opts.autoPlay&&!me._timer){me._timer=setTimeout(function(){me.slideTo(me.index+1);me._timer=null},opts.interval)};return me},stop:function(){var me=this;if(me._timer){clearTimeout(me._timer);me._timer=null};return me}})})(gmu,gmu.$);(function(gmu,$,undefined){gmu.Slider.option('dots',true,function(){var dots=$('.banner .btn');var len=$('.bannerpic li').length;var updateDots=function(to,from){dots.find('li').removeClass('s');dots.find('li').eq(to).addClass('s')};this.on('done.dom',function(){for(var i=0;i<len;i++)$('.banner .btn').append("<li class='amn3'></li>");$('.banner .btn li').last().addClass('last')});this.on('slide',function(e,to,from){updateDots.call(this,to,from)});this.on('ready',function(){updateDots.call(this,this.index)})})})(gmu,gmu.$);