<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @file
 */

namespace MediaWiki\Extension\AADeckView;

class Hooks implements \MediaWiki\Hook\BeforePageDisplayHook {


	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/BeforePageDisplay
	 * @param \OutputPage $out
	 * @param \Skin $skin
	 */
	public function onBeforePageDisplay( $out, $skin ) : void {
    //wfDebug("AADeckView test from onbeforepagedisplay");
		//$out->addModules( 'ext.aaDeckView.indexQuick' );
		$out->addScriptFile( '/extensions/AADeckView/resources/ext.aaDeckView/main.indexQuick.js' );
		if ( substr($out->getPageTitle(), 0, strlen('Deck Search')) === 'Deck Search' ) {
			$out->addModules('ext.aaDeckView.indexDeckSearch');
		}
		if ( substr($out->getPageTitle(), 0, strlen('Card Gallery')) === 'Card Gallery' ) {
			$out->addModules('ext.aaDeckView.indexGallery');
		}
		if ( substr($out->getPageTitle(), 0, strlen('Spoilers')) === 'Spoilers' ) {
			$out->addModules('ext.aaDeckView.indexGallery');
		}
		if ( substr($out->getPageTitle(), 0, strlen('Rise of the Keyraken')) === 'Rise of the Keyraken' ) {
			$out->addModules('ext.aaDeckView.indexGallery');
		}
		if ( substr($out->getPageTitle(), 0, strlen('Deck:')) === 'Deck:' ) {
			$out->addModules('ext.aaDeckView.indexDeckView');
		}
		else {
			$out->addModules( 'ext.aaDeckView.indexCommon' );
		}

		$localized_title = [];
		preg_match('/<localetitle>(.*?)<\/localetitle>/', $out->getHTML(), $localized_title);
		
		if (count($localized_title) > 1) {
			$out->setPageTitle($localized_title[1]);
		}


		if ( substr($out->getPageTitle(), 0, strlen('User')) !== 'User' && substr($out->getPageTitle(), 0, strlen('Special')) !== 'Special' && substr($out->getPageTitle(), 0, strlen('Template:')) !== 'Template:') {
			$out->addInlineScript( <<<'EOT'
				// Hotjar Tracking Code for archonarcana.com
				    (function(h,o,t,j,a,r){
					h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
					h._hjSettings={hjid:2302354,hjsv:6};
					a=o.getElementsByTagName('head')[0];
					r=o.createElement('script');r.async=1;
					r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
					a.appendChild(r);
				    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
EOT
			);
		}

		$out->addInlineScript( <<<'EOT'
(function(f,b){if(!b.__SV){var e,g,i,h;window.mixpanel=b;b._i=[];b.init=function(e,f,c){function g(a,d){var b=d.split(".");2==b.length&&(a=a[b[0]],d=b[1]);a[d]=function(){a.push([d].concat(Array.prototype.slice.call(arguments,0)))}}var a=b;"undefined"!==typeof c?a=b[c]=[]:c="mixpanel";a.people=a.people||[];a.toString=function(a){var d="mixpanel";"mixpanel"!==c&&(d+="."+c);a||(d+=" (stub)");return d};a.people.toString=function(){return a.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms track_with_groups add_group set_group remove_group register register_once alias unregister identify name_tag set_config reset opt_in_tracking opt_out_tracking has_opted_in_tracking has_opted_out_tracking clear_opt_in_out_tracking start_batch_senders people.set people.set_once people.unset people.increment people.append people.union people.track_charge people.clear_charges people.delete_user people.remove".split(" ");
for(h=0;h<i.length;h++)g(a,i[h]);var j="set set_once union unset remove delete".split(" ");a.get_group=function(){function b(c){d[c]=function(){call2_args=arguments;call2=[c].concat(Array.prototype.slice.call(call2_args,0));a.push([e,call2])}}for(var d={},e=["get_group"].concat(Array.prototype.slice.call(arguments,0)),c=0;c<j.length;c++)b(j[c]);return d};b._i.push([e,f,c])};b.__SV=1.2;e=f.createElement("script");e.type="text/javascript";e.async=!0;e.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?
MIXPANEL_CUSTOM_LIB_URL:"file:"===f.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";g=f.getElementsByTagName("script")[0];g.parentNode.insertBefore(e,g)}})(document,window.mixpanel||[]);
mixpanel.init("b9705711d013fadf78347c631a82fef8", {batch_requests: true})
EOT
		);

		try {
			$match = [];
			$result = preg_match("/^(.*)\/locale\/(.*)$/", $out->getTitle(), $match);
			if ( count($match) > 2 ) {
				$title = $match[1];
				$locale = $match[2];
				$out->setPageTitle( '...' );
				$out->clearHTML();
				$out->addHTML('<div class="cardEntry" data-locale="' . $locale . '" data-name="' . $title . '"></div>');
			}
		} finally {}

		if ( $out->getTitle()->getNsText() == "Deck" ) {
			$out->setPageTitle( '' );
			$out->clearHTML();
			$html = <<<EOD
<html>
<style>

/* link formatting */
.mw-body a:link {
  text-decoration: none;
  color: #1c2b9c;
  border-bottom: 2px solid transparent;
}

.mw-body a:visited {
  text-decoration: none;
  border-bottom: 2px solid transparent;
  color: #1c2b9c;
}

.mw-body a:hover {
  color: #000000;
  text-decoration: underline;
  border-bottom: 2px solid transparent;
}

/* decklist preview */
.decklist-viewer {
  display: grid;
  grid-template-columns: auto 150px 150px 150px;
  grid-template-rows: auto 40px 60px auto 40px 50px;
  grid-gap: 5px;
  background-color: #f0f0f0;
  padding: 5px 5px 5px 5px;
  max-width: 850px;
  box-sizing:border-box;
}

.decklist-viewer > div {
  text-align: center;
}

.decklist-image {
  grid-row-start: 1;
  grid-row-end: 7;
  min-width: 200px;
  max-width: 400px;
  padding: 5px 8px 7px 7px;
  margin: 5px;
  border-radius: 3%;
  background-color: #c0c0c0;
  display:flex;
  align-items:center;
  justify-content:center;
}

.decklist-image img {
  max-width: 100%;
  height: auto;
  border-radius: 3%;
  filter: drop-shadow(2px 2px 0px #000000);
}

.decklist-title {
  grid-column-start: 2;
  grid-column-end: 5;
  font-family: castoro;
  font-size: 2.3em;
  line-height: 1em;
  border-bottom: 1px dashed #a0a0a0;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px 5px 5px 5px;
}

.set-name {
  grid-column-start: 2;
  grid-column-end: 5;
  font-family: zilla slab;
  font-size: 1.5em;
  display: flex;
  justify-content: center;
  align-items: flex-end;
}

.set-houses img {
  filter: drop-shadow(3px 3px 0px #303030);
  margin-left: 3px;
  margin-right: 3px;
  height: 40px;
  width: 40px;
}

.set-houses {
  grid-column-start: 2;
  grid-column-end: 5;
  border-bottom: 1px dashed #a0a0a0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
}

.deck-info {
  grid-column-start: 2;
  grid-column-end: 5;
  display: flex;
}

.card-types,
.card-rarities,
.card-enhancements {
  font-family: lato;
  line-height: 2em;
  padding:10px 0px 10px 0px;
  flex:1;
}

.card-types:first-line,
.card-rarities:first-line,
.card-enhancements:first-line {
  font-weight: 500;
  font-family: zilla slab;
  font-size: 1.2em;
  color: #505050;
}

.card-types img,
.card-rarities img,
.card-enhancements img {
  filter: drop-shadow(1px 1px 0px #303030);
  height: 22px;
  width: 22px;
   margin-bottom:2px;
}

.deck-aember {
  grid-column-start: 2;
  grid-column-end: 5;
  font-family: lato;
  border-bottom: 1px dashed #a0a0a0;
  padding: 5px 5px 5px 5px;
}

   .links {
     grid-column-start:2;
     grid-column-end:5;
     display:flex;
   }

.link-1,
.link-2,
.link-3 {
  font-family: lato;
  font-size: 0.9em;
  display: flex;
  flex:1;
  justify-content: center;
  align-items: center;
  padding: 5px 0px 5px 0px;
}

.decklist-image a:hover {
  border-bottom:0px !important;
}


@media screen and (min-width:901px) {
.mw-body h2 {
  font-family: castoro !important;
  border-bottom:1px solid #000000 !important;
}

.mw-body h2:after {
  border:0px solid #000000 !important;
}

}

@media screen and (max-width: 900px) {

  .mw-body h2 {
  font-family: castoro !important;
  border-bottom:1px solid #000000 !important;
  }

  .mw-body h2:after {
  border:0px solid #000000 !important;
  }


  .decklist-viewer {
    display: grid;
    grid-template-columns: calc(33% - 2px) calc(33% - 3px) calc(33% - 2px);
    grid-gap: 5px;
    background-color: #ffffff;
    padding: 5px 5px 5px 5px;
    max-width: 850px;
    border:0px;
  }

  .decklist-image {
    grid-column-start: 1;
    grid-column-end: 4;
    min-width: 250px;
    max-width: 320px;
    margin-left: auto;
    margin-right: auto;
    text-align:center;
    padding: 0px;
    background-color:#ffffff;
  }

  .decklist-title {
    grid-column-start: 1;
    grid-column-end: 4;
    font-size: 1.8em;
    line-height: 1em;
    padding: 10px 5px 5px 5px;
  }

  .set-name {
    grid-column-start: 1;
    grid-column-end: 4;
    font-size: 1.3em;
  }

  .set-houses {
    grid-column-start: 1;
    grid-column-end: 4;
    border-bottom: 1px dashed #a0a0a0;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 0px 0px 10px 0px;
  }
  .deck-info {
    grid-column-start: 1;
    grid-column-end: 4;
  }

   .card-types, .card-rarities, .card-enhancements {
      padding:0px 0px 10px 0px;
  }

  .card-types:first-line,
  .card-rarities:first-line,
  .card-enhancements:first-line {
    font-size: 1em;
    font-family: lato;
    font-weight:600;
  }

  .deck-aember {
    grid-column-start: 1;
    grid-column-end: 4;
    padding: 10px 5px 10px 5px;
  }


   .links {
     grid-column-start:1;
     grid-column-end:4;
   }

  .link-1,
  .link-2,
  .link-3 {
    font-size: 0.9em;
    padding: 5px 0px 5px 0px;
  }

}

/* adjustments for very small screens */
@media screen and (max-width:370px) {

  .deck-info {
     display:block;
  }

  .card-types,
  .card-rarities,
  .card-enhancements {
    grid-column-start: 1;
    grid-column-end: 4;
    border-bottom:1px dashed #a0a0a0;
  }

  .deck-aember {
    grid-column-start: 1;
    grid-column-end: 4;
    padding: 0px 0px 5px 0px;
  }


}



/*
 *
 * Formatting for the card list preview
 *
 */

  .card-preview-gallery {
    width: 100%;
    overflow: hidden;
    display: flex; /* I hate internet explorer */
    flex-wrap: wrap;
    display: grid; /* every other browser gets a pretty grid */
    grid-column-gap:10px;
    grid-row-gap:10px;
  }

.card-preview {
  position: relative;
    min-width: 150px;
    max-width: 300px;
    overflow: hidden;
    height: auto;
  transition:all .5s ease-in-out;
  }

.card-preview:hover {
   filter:brightness(.8);
   cursor:pointer;
}

  .card-preview img {
    width: 100%;
    height: auto;
  }


.enhanced-card {
  position:absolute;
  height:100%;
  width:100%;
  display:block;
  top:0px;
  left:0px;
  content:"";
  z-index:5;
  opacity:1;
  border-radius:5%;
}

.enhanced-card:before {
  position:absolute;
  top:40%;
  right:0px;
  content:"Enhanced";
  font-family:lato;
  padding:3px 5px 3px 10px;
  background-color:#353331;
  color:white;
  opacity:1;
  font-size:.9em;
}

@media screen and (max-width:600px) {
  .card-preview-gallery {
    grid-template-columns: repeat(2, 1fr);
    grid-column-gap:5px;
    grid-row-gap:5px;
  }

  .enhanced-card:before {
  font-size:.85em;
}

}

@media screen and (min-width: 601px) and (max-width: 900px) {
  .card-preview-gallery {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media screen and (min-width: 901px) and (max-width: 1200px) {
  .card-preview-gallery {
    grid-template-columns: repeat(4, 1fr);
  }
}

@media screen and (min-width: 1201px) {
  .card-preview-gallery {
    grid-template-columns: repeat(5, 1fr);
  }

}

.loading-screen {
  height:150px;
  width:150px;
  background-color:#c0c0c0;
  position:relative;
  border-radius:50%;
  overflow:hidden;
}

.outer-circle {
  position:absolute;
  top:0px;
  left:0px;
  height:100%;
  width:100%;
    background-image:conic-gradient(
				#000000 0,
				#000000 15%,
				#c0c0c0 0,
				#c0c0c0 20%,
				#303030 0,
				#303030 35%,
				#c0c0c0 0,
				#c0c0c0 40%,
				#000000 0,
				#000000 55%,
				#c0c0c0 0,
				#c0c0c0 60%,
				#505050 0,
				#505050 75%,
				#c0c0c0 0,
				#c0c0c0 80%,
				#303030 0,
				#303030 95%,
				#c0c0c0 0,
				#c0c0c0 100%
		);
  -webkit-animation-name: spin;
  -webkit-animation-duration: 30s;
  -webkit-animation-iteration-count: infinite;
  -webkit-animation-timing-function: linear;
  border-radius:50%;
}

.inner-circle {
  position:absolute;
  top:10%;
  left:10%;
  height:80%;
  width:80%;
  background-color:#c0c0c0;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  /*font-family:verdana;*/
}

@-moz-keyframes spin {
    from { -moz-transform: rotate(0deg); }
    to { -moz-transform: rotate(360deg); }
}
@-webkit-keyframes spin {
    from { -webkit-transform: rotate(0deg); }
    to { -webkit-transform: rotate(360deg); }
}
@keyframes spin {
    from {transform:rotate(0deg);}
    to {transform:rotate(360deg);}
}


</style>

<!-- import fonts -->
  <link href='https://fonts.googleapis.com/css?family=Castoro' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Mate SC' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet'>
  <link href='https://fonts.googleapis.com/css?family=Zilla Slab' rel='stylesheet'>

<!-- archon card / decklist -->
<h2>Decklist</h2>
  <div class="decklist-viewer">
    <div class="decklist-image"><div class="loading-screen">
  <div class="outer-circle"></div>
  <div class="inner-circle">Loading<br>Decklist</div>
</div>
</div>
    <div class="decklist-title">Decklist</div>
    <div class="set-name"><br></div>
    <div class="set-houses"><br>
    </div>
    <div class="deck-info">
    <div class="card-types">
      <br><br><br><br><br>
    </div>
    <div class="card-rarities">
      <br><br><br><br><br>
    </div>
    <!-- remove this entire div if the deck is pre-MM -->
    <div class="card-enhancements">
     <br><br><br><br><br>
    </div>
    </div>
    <div class="deck-aember"></div>
    <div class="links">
      <div class="link-1"></div>
      <div class="link-2"></div>
      <div class="link-3"></div>
    </div>
  </div>
</html>
EOD;
			$out->addHTML($html);
		}
	}

}
