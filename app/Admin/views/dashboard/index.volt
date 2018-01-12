{% extends 'base.volt' %}

{% block title %}Dashboard{% endblock %}

{% block scripts %}
{{ javascript_include('js/admin/pages/dashboard.js') }}
{% endblock %}

{% block content %}
<h2>This is the Admin Dashboard page</h2>
<p>This is a test Admin Dashboard page using volt. Current date and time: {{ date('d/m/Y H:i:s') }}</p>
<p>A user is {% if not userLogged %}Not {% endif %}Logged in to your admin</p>
{% if userLogged %}
    <p>His name is: <strong>{{ user['name'] }}</strong></p>
{% endif %}
{% endblock %}