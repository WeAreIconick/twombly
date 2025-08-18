<?php
/**
 * Title: A Portfolio Item
 * Slug: twombly/portfolio-item
 * Description: A format for the easy creation of portfolio items.
 * Categories: portfolio
 * Keywords: portfolio, twombly
 * Post Types: post
 * Inserter: yes
 */
?>
<!-- wp:group {"metadata":{"name":"Portfolio Item"},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:heading {"level":6,"metadata":{"name":"Subtitle"},"style":{"elements":{"link":{"color":{"text":"var:preset|color|text"}}}},"textColor":"text"} -->
<h6 class="wp-block-heading has-text-color has-link-color">A subtitle.</h6>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img/></figure>
<!-- /wp:image -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column {"width":"66.66%","metadata":{"name":"Description"}} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:heading {"level":5} -->
<h5 class="wp-block-heading">About the item</h5>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam euismod, erat cursus scelerisque mattis, lorem eros pellentesque diam, eu posuere ex mauris ac risus. Aenean quis lacus leo. Pellentesque ornare, velit id feugiat ornare, neque urna aliquet dolor, eu pulvinar dolor lectus et urna. Donec luctus tristique ex, id bibendum risus aliquet sed. Duis sit amet accumsan lacus. Quisque dictum enim quis feugiat accumsan. Donec nec tincidunt velit, et malesuada mi. Praesent dapibus efficitur tortor quis pulvinar. Nulla eu faucibus dolor. Vestibulum at neque ac metus vulputate dictum. Etiam convallis scelerisque diam sollicitudin aliquet. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Vivamus nec arcu id odio maximus tempus. Maecenas eros velit, ullamcorper sit amet eros et, molestie feugiat felis. Vestibulum finibus interdum lacinia.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Ut tincidunt tristique ligula, quis imperdiet erat dictum quis. Ut malesuada vitae velit vitae molestie. Suspendisse viverra tellus ac dui blandit pretium sit amet nec ligula. Quisque varius sagittis libero eget vehicula. Donec vitae porta tortor. Nunc fermentum ex nec augue volutpat consequat. Nullam dapibus ullamcorper lobortis. Vestibulum convallis quis arcu luctus viverra. Morbi vitae dolor ut ipsum semper aliquam. Duis et bibendum lectus. Duis sollicitudin ex ac tortor tempor, sed interdum dolor dignissim. Maecenas luctus tortor tortor, ullamcorper vulputate mauris tempus sit amet.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Morbi vel tempor est, vel auctor ante. Phasellus interdum non massa sit amet fringilla. In ac fringilla massa. Cras sed lectus eleifend, iaculis tellus id, varius neque. Mauris mauris risus, posuere vitae interdum efficitur, cursus a massa. Donec a tempus ipsum, vel fermentum eros. Nunc vestibulum ac purus quis convallis. Nunc id augue pellentesque, lobortis mi vel, bibendum arcu. Maecenas lobortis lorem et urna vulputate molestie. Quisque dapibus pharetra condimentum. Etiam accumsan odio tortor, et rhoncus leo lobortis et. Etiam fermentum augue sapien. Nam in bibendum nulla, eu lacinia quam. Mauris sit amet felis sed augue luctus auctor volutpat sed orci.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Duis est sapien, consectetur ac consequat eget, rutrum in leo. Nam elit arcu, ornare eu auctor ac, porttitor in neque. Vestibulum vitae vestibulum dolor. Quisque vehicula massa ut ante congue hendrerit. Proin blandit enim vitae orci rutrum finibus. Morbi id lorem tristique odio scelerisque imperdiet et volutpat turpis. Sed aliquam sit amet ipsum vel finibus. In maximus aliquam tortor at ornare. Vivamus aliquet diam augue, vitae imperdiet massa pulvinar sed. Vivamus non rutrum justo. Duis finibus feugiat massa sed convallis. Aliquam vestibulum eu lacus ac accumsan. Nulla id dui vulputate, convallis lorem eget, faucibus tortor. Vestibulum et libero vel lacus fringilla sagittis. Nam euismod nisi sit amet enim pretium eleifend. Fusce et turpis eu ipsum ornare imperdiet ut ut nisl.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Vestibulum diam velit, cursus volutpat sollicitudin a, suscipit et turpis. Suspendisse accumsan ut erat sagittis laoreet. Pellentesque auctor bibendum mauris, varius pretium neque faucibus ut. Sed imperdiet orci vitae libero lacinia, eu molestie turpis ornare. Ut sit amet convallis est. Cras mauris urna, blandit at metus sit amet, efficitur faucibus odio. In eu volutpat elit. Donec imperdiet, ipsum vel ullamcorper venenatis, massa augue fermentum tellus, sed viverra nisl felis nec sapien. Sed consectetur rhoncus lorem, et ullamcorper ligula condimentum nec. Mauris interdum varius sapien, at tincidunt mauris ullamcorper nec. Nam vitae nisi lectus. Donec vitae auctor ex. Praesent eu molestie metus. Pellentesque imperdiet mauris ac nisl laoreet finibus. Aliquam euismod elit nec eros auctor, in iaculis massa ullamcorper. Etiam vel vulputate massa.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%","metadata":{"name":"Details"}} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:group {"metadata":{"name":"Details Card"},"className":"is-style-twombly-card","style":{"position":{"type":"sticky","top":"0px"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group is-style-twombly-card"><!-- wp:heading {"level":5} -->
<h5 class="wp-block-heading">Features</h5>
<!-- /wp:heading -->

<!-- wp:list {"style":{"spacing":{"padding":{"right":"0","left":"var:preset|spacing|40"}}}} -->
<ul style="padding-right:0;padding-left:var(--wp--preset--spacing--40)" class="wp-block-list"><!-- wp:list-item -->
<li>Item One!</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Second thing</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>A secret but pretty awesome third thing!</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button">Buy for $99</a></div>
<!-- /wp:button -->

<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button">Unlock with Pro!</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->