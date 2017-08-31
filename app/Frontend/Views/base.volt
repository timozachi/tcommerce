<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{% block title %}{% endblock %} - TCommerce</title>

    <link rel="stylesheet" href="{{ url('css/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/bootstrap/bootstrap-theme.min.css') }}">
    {% block styles %}{% endblock %}

    <script type="text/javascript" src="{{ url('js/jquery-2.1.3.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('js/bootstrap/bootstrap.min.js') }}"></script>

</head>

<body>

<div class="container">
    {% block content %}{% endblock %}
</div>

{% block modals %}{% endblock %}

{% block scripts %}{% endblock %}

</body>

</html>
