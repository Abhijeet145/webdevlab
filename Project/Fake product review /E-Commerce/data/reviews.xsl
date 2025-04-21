<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <!-- Match the root of the XML document -->
    <xsl:template match="/reviews">
        <html>
            <head>
                <title>Product Reviews</title>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    table, th, td {
                        border: 1px solid black;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                </style>
            </head>
            <body>
                <h2>Product Reviews</h2>
                <table>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Review Text</th>
                        <th>Sentiment</th>
                        <th>Is Fake</th>
                        <th>Timestamp</th>
                    </tr>
                    <!-- Loop through each review -->
                    <xsl:for-each select="review">
                        <tr>
                            <td><xsl:value-of select="product_id" /></td>
                            <td><xsl:value-of select="product_name" /></td>
                            <td><xsl:value-of select="text" /></td>
                            <td><xsl:value-of select="sentiment" /></td>
                            <td><xsl:value-of select="is_fake" /></td>
                            <td><xsl:value-of select="timestamp" /></td>
                        </tr>
                    </xsl:for-each>
                </table>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
