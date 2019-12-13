<style>
    @page {
        size: auto;
        margin-left: 10mm;
        margin-right: 10mm;
        margin-top: 10mm;
        margin-footer: 5mm;

        margin-left: 0;
        margin-right: 0;
        margin-top: 0;
        margin-header: 0;
        margin-footer: 0;
        margin-bottom: 0;
    }

    body, * {
        font-family: 'PT Sans', 'ptsans', ptsans;
        line-height: 1em;
    }

    body {
        font-size: 10pt;
    }


    .page-container {
        width: 176mm;
        height: 249mm;

        margin: 0;
        padding: 0;
        border: 0;
        border: solid 1px black;

    }

    .content-container {
        width: 176mm;
        height: 229mm;

        margin: 0;
        padding: 10mm 10mm 0 10mm;
        border: 0;

    }

    .footer-container {
        margin: 0;
        border: 0;
        padding: 0 10mm 0 10mm;
    }

    .page-footer {
        width: 100%;
        padding-top: 2mm;
        border-top: solid 0.2mm #d4d4d4;
        font-weight: bold;
    }

    .inner {
        margin: 0;
        padding: 0;
        border: 0;
    }

    .column {
        float:left;
        height: 185mm;
        overflow: hidden;

        width: 47mm;

        border: 0;
        margin: 0;
        padding: 0;
        padding-right: 4mm;
    }

    .column.colum-last {
        width: 47mm;
        padding-right: 0;
    }

    .box {
        width: 47mm;
        height: 10mm;
        max-height: 10mm;
        min-height: 10mm;
        padding: 1mm;
        margin: 0;
        border: 0;
        overflow: hidden;
        vertical-align: middle;
    }

    .header-box {
        color: white;
        font-weight: bold;
        background-color: #431340;
    }

    .one-line {
        height: 0.5em;
    }


    h1 {
        font-weight: bold;
        font-size: 10pt;
        padding-top: 3mm;
    }

    hr {
        margin: 1mm 0 1mm 0;
        border: 0;
        height: 1px;
        background-color: #d4d4d4;
    }


    .highlight {
        color: #431340;
    }

    table.services {
        table-layout:fixed;
        width: 100%;
    }

    table.services thead th {
        background-color: #431340;
        color: white;
        font-weight: bold;

    }

    table.services td, th {
        margin: 0;
        border: 0;
        padding: 1mm;

        height: 10mm;
        max-height: 10mm;
        min-height: 10mm;

        overflow: hidden;

        width: 53mm;
        max-width: 53mm;

        text-align: left;
        vertical-align: middle;

        padding: 1mm;

        font-size: 10pt;
        font-family: ptsans;

        hyphens: auto;
        position: relative;
    }

    table.services .last-column {
        border-right: 0;
    }

    table.services tbody td, tbody th {
        color: #5a6268;
        overflow: hidden;
    }

    table.services tbody th {
        line-height: 0.9em;
    }

    table.services .line {
        border: 0;
        margin: 0;
        padding: 0;
        height: 1mm;
        max-height: 1mm;
        overflow: hidden;
        border-top: solid 0.2mm #d4d4d4;
        background-color: white !important;
    }

    table services.large td,
    table services.large th {
        width: 53mm;
        max-width: 53mm;
        min-width: 53mm;
    }




    table.services tr.highlight-row td, tr.highlight-row th {
        background-color: #e2d5e5;
    }

    table.services tr td.column-spacer,
    table.services tr th.column-spacer
    {
        width: 3mm;
        margin: 0;
        padding: 0;
        background-color: white;
    }




    .service-logos {
        float: left;
    }

    img.service-logo {
        float: left;
    }

    table.icon-info {
        margin-top: 5mm;
        border-collapse: collapse;
    }


    table.icon-info td {
        padding: 0;
        border: 0;
        margin: 0;
        width: 19mm;
        color: #5a6268;
        text-align: center;
        font-weight: bold;
        font-size: 10pt;
        font-style: italic;
        vertical-align: top;
    }

    table.icon-info tbody td {
        padding-top: 2mm;
    }

    td.info-column {
        vertical-align: top;
    }


    table.services td.info-column ul {
        margin: 2mm 0 2mm 0;
        padding-left: 20px;
        list-style-position: outside;
    }

    table.services td.info-column p {
        padding: 2mm 0 2mm 0;
        font-weight: bold;
    }

    table.bullet {
        border-collapse: collapse;
        margin: 0;
        padding: 0;
        border: 0;
    }

    table.bullet td {
        border: 0;
        margin: 0;
        padding: 0;
        height: auto;
        hyphens: none;
    }

    table.bullet td.bullet {
        width: 2mm;
    }

    table.bullet td.info {
        width: 44mm;
        min-width: 44mm;
        max-width: 44mm;
    }
    table.bullet td.heading {
        font-weight: bold;
    }

    table.bullet td.spacer {
        height: 2mm;
    }

    div.infobox {
        padding: 1mm 1mm 1mm 4mm;
        color: #5a6268;
        hyphen: auto;
    }

    div.infobox p {
        margin: 0 0 1mm 0;
        padding: 0;
    }

    div.infobox ul {
        margin: 1mm 0 2mm 0;
        padding: 0;
        padding-left: 3.5mm;
    }

</style>
