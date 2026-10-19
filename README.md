# Egsn_FeaturedProduct

Exibe, como primeiro elemento do conteúdo principal da homepage (tema Luma), uma caixa com um único produto em destaque: nome, preço, imagem base e quantidade disponível para venda. A quantidade é atualizada periodicamente via AJAX, sem recarregar a página. Clicar na caixa leva à página do produto.

## Requisitos

- Magento Open Source 2.4.6 com tema Luma
- MSI habilitado (`Magento_InventorySalesApi`)
- PHP ~8.1 || ~8.2 || ~8.3

## Instalação

Via Composer:

```bash
composer require egsn/module-featured-product
bin/magento module:enable Egsn_FeaturedProduct
bin/magento setup:upgrade
```

Ou copiando o módulo para `app/code/Egsn/FeaturedProduct` e rodando os dois últimos comandos.

## Configuração

Lojas > Configuração > Egsn > Featured Product (escopo default/website/store view):

- **Enabled**: liga/desliga a caixa.
- **Product SKU**: SKU do produto em destaque. Precisa ser habilitado, visível no catálogo, associado ao website e de um tipo com gestão de estoque (simples, virtual ou baixável).
- **Stock Refresh Interval (seconds)**: intervalo de atualização da quantidade (3 a 3600, padrão 10).

Após salvar, limpe o cache `full_page`.

## Funcionalidades

- Bloco inserido via `referenceContainer name="content"` com `before="-"` em `cms_index_index.xml`, ocupando 100% da largura do `column.main`.
- Nome, preço (renderizador padrão `product.price.render.default`, respeitando preço especial e exibição de impostos) e imagem base (`product_base_image`) renderizados no servidor: compatível com Full Page Cache e com SEO.
- O bloco implementa `IdentityInterface`: a homepage em cache é invalidada quando o produto é alterado.
- Quantidade disponível para venda (`GetProductSalableQtyInterface`, já descontando reservas e qty mínima) servida por `GET /egsn_featuredproduct/stock/qty`, com headers `no-store`, sem parâmetros: o endpoint só expõe o produto configurado.
- Componente Knockout (`uiComponent`) declarado via `jsLayout` nos block arguments; URL e intervalo são injetados em runtime pelo bloco.
- Polling encadeado (`setTimeout` após a resposta, sem acumular requisições) e pausado quando a aba está em segundo plano.

## Changelog

Todas as mudanças relevantes deste módulo são documentadas neste arquivo.
O formato segue boas práticas inspiradas em Keep a Changelog e Semantic Versioning.

### [Unreleased]
#### Added
#### Changed
#### Fixed

### [1.0.0] - 2026-10-17
#### Added
- Versão inicial do módulo: caixa de produto em destaque na homepage com quantidade disponível para venda atualizada em tempo real.

