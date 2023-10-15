<!DOCTYPE html>
<html>
<head>
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="/swagger/swagger-ui.css">
</head>
<body>
<div id="swagger-ui"></div>
<script src="/swagger/swagger-ui-bundle.js"></script>
<script src="/swagger/swagger-ui-standalone-preset.js"></script>
<script>
    const ui = SwaggerUIBundle({
        url: "/openapi.yaml", // Path to your generated OpenAPI spec
        dom_id: "#swagger-ui",
    });
</script>
</body>
</html>
