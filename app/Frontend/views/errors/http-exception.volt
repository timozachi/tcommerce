{% extends 'base.volt' %}

{% block title %}HTTP Error ({{ code }}){% endblock %}

{% block content %}
<h3>(Frontend) HTTP Error ({{ code }}): {{ message }}</h3>
{% endblock %}