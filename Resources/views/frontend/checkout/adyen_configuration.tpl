{if $sAdyenConfig}
    <div data-shopLocale='{$sAdyenConfig.shopLocale}'
         data-adyenOriginKey='{$sAdyenConfig.originKey}'
         data-adyenEnvironment='{$sAdyenConfig.environment}'
         data-adyenPaymentMethodsResponse='{$sAdyenConfig.paymentMethods}'
         data-resetSessionUrl='{url controller="Adyen" action="ResetValidPaymentSession"}'
         {if $mAdyenSnippets}data-adyensnippets="{$mAdyenSnippets}"{/if}
         class="adyen-payment-selection adyen-config">
    </div>
{/if}
