{% extends 'base.volt' %}

{% block title %}Admin Login{% endblock %}

{% block scripts %}
    {{ javascript_include('js/admin/pages/login.js') }}
{% endblock %}

{% block content %}
    <div class="login">
        <h2 class="text-center">Login to our system</h2>
        {{ flashSession.output() }}
        <form action="{{ url(['for':'admin-index-login-post']) }}" method="post">
            <div class="form-group">
                <label for="txtEmail">Email:</label>
                <input type="text" class="form-control" id="txtEmail" name="email">
            </div>
            <div class="form-group">
                <label for="txtPassword">Password:</label>
                <input type="password" class="form-control" id="txtPassword" name="password">
            </div>
            <div class="form-group text-right">
                <input type="submit" class="btn btn-default" value="Submit">
            </div>
        </form>
    </div>
{% endblock %}