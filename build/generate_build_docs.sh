#!/bin/bash

NOW="`date`"

function cat_html()
{
    local text_file="$1"

    cat "$text_file" \
        | sed "s/&/\\&amp;/g" \
        | sed "s/</\\&lt;/g" \
        | sed "s/>/\\&gt;/g" \
        | sed "s/\"/\\&quot;/g" \
        | sed "s/'/\\&#039;/g"
}

cat <<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>AthosHun\HTMLFilter Build $NOW</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="utf-8" />
    <meta name="description" content="AthosHun\HTMLFilter Build - $NOW" />
    <style type="text/css">
    <!--
    html, body {
      background-color: #ffffff;
      color: #000000;
    }
    * {
      font-family: Verdana, Geneva, sans-serif;
    }
    a {
      color: #000070;
    }
    div.column {
      float: left;
      width: 50%;
    }
    div.last {
      clear: right;
    }
    img {
      width: 100%;
      background-color: #f0f0f0;
    }
    pre {
      font-family: Monaco, "Liberation Mono", "Lucida Console", monospace;
      font-size: 1em;
      margin: 10px;
      padding: 7px;
      min-width: 80%;
      background-color: #e0e0e0;
      color: #000000;
    }
    -->
    </style>
  </head>
  <body>
    <h1>AthosHun\HTMLFilter Build</h1>
    <p>Generated: $NOW</p>
    <h2>Table of contents</h2>
    <ul>
      <li><p><a href="#readme">README</a></p></li>
      <li><p><a href="#phpunit">PHPUnit Coverage</a></p></li>
      <li><p><a href="#more">More build artifacts</a></p></li>
    </ul>
    <h2 id="readme">README</h2>
    <pre>`cat_html README.md`</pre>
    <h2 id="phpunit">PHPUnit Coverage</h2>
    <pre>`cat_html docs/reports/phpunit_coverage.txt`</pre>
    <h2 id="more">More build artifacts</h2>
    <ul>
      <li><p><a href="api/index.html">API documentation (HTML)</a></p></li>
      <li>
        <h3>PHPUnit</h3>
        <ul>
          <li><p><a href="reports/phpunit.html">Testdox (HTML)</a></p></li>
          <li><p><a href="reports/phpunit_coverage/index.dashboard.html">Dashboard (HTML)</a></p></li>
          <li><p><a href="reports/phpunit_coverage/index.html">Coverage report (HTML) </a></p></li>
          <li><p><a href="reports/phpunit_clover.xml">Clover (XML)</a></p></li>
        </ul>
      </li>
    </ul>
  </body>
</html>
HTML
