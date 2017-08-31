{% extends 'base.volt' %}

{% block title %}Home{% endblock %}

{% block scripts %}
{{ javascript_include('js/pages/index.js') }}
{% endblock %}

{% block content %}
<h2>This is the home page</h2>
<p>This is a test home page using volt. Current date and time: {{ date('d/m/Y H:i:s') }}</p>
{% endblock %}