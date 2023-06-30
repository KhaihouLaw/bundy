<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Leave Report</title>
    <style>
        .container {
            font-family: Nunito, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            padding: 10px;
            border-radius: 10px;
        }
        .container table.report {
            width: 100%;
            border-collapse: collapse;
            font-size: 8px;
        }
        table.report th {
            padding: 4px;
        }
        th, td {
            border: 2px solid rgba(128, 128, 128, 0.39);
        }
        td {
            padding-top: 2px !important;
            padding-bottom: 2px !important;
        }
        table.report tbody {
            text-align: center;
        }
        span.date-range {
            color: rgb(51, 51, 51);
        }
        table.report div.overall-total {
            padding: 3px;
            border-radius: 10px;
            background-color: rgba(150, 150, 150, 0.171) !important;
        }
    </style>
  </head>
  <body>
    <div class="container">
        @include('pdf.leave-report.component.table')
    </div>
  </body>
</html>
