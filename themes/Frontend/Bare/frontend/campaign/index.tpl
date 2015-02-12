{extends file='parent:frontend/home/index.tpl'}

{block name='frontend_index_header_canonical'}
<link rel="canonical" href="{url emotionId=$emotionId}" />
{/block}


{* Keywords *}
{block name="frontend_index_header_meta_keywords"}{if $seo_keywords}{$seo_keywords}{/if}{/block}

{* Description *}
{block name="frontend_index_header_meta_description"}{if $seo_description}{$seo_description}{/if}{/block}


{* Topseller *}
{block name='frontend_home_right_topseller'}{/block}

{* Promotion *}
{block name='frontend_home_index_promotions'}
    <div class="emotion--wrapper"
         data-controllerUrl="{url module=widgets controller=emotion action=index emotionId=$emotion.id controllerName=$Controller}"
         data-availableDevices="{$emotion.devices}"
         data-showListing="false">
    </div>
{/block}

{* Sidebar left *}
{block name='frontend_index_content_left'}{/block}

{* Sidebar right *}
{block name='frontend_index_content_right'}{/block}