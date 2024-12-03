`timescale 1ns / 1ps
//////////////////////////////////////////////////////////////////////////////////
// Company: 
// Engineer: 
// 
// Create Date: 05/31/2024 01:12:27 PM
// Design Name: 
// Module Name: ALU_32
// Project Name: 
// Target Devices: 
// Tool Versions: 
// Description: 
// 
// Dependencies: 
// 
// Revision:
// Revision 0.01 - File Created
// Additional Comments:
// 
//////////////////////////////////////////////////////////////////////////////////


module ALU_32(
    input [31:0] A_in, B_in,
    input [3:0] ALU_Sel,
    output [31:0] ALU_Out,
    output reg Carry_Out,
    output Zero,
    output reg Overflow = 1'b0
    );
    reg [31:0] ALU_Result;
    reg [32:0] temp;
    reg [32:0] twos_com;
    
    assign ALU_Out = ALU_Result;
    assign Zero = (ALU_Result == 0);
    
    always @(*)
    begin
        Overflow = 1'b0;
        Carry_Out = 1'b0;
        case(ALU_Sel)
            4'b0000:
                ALU_Result = A_in & B_in;
            4'b0001:
                ALU_Result = A_in | B_in;
            4'b0010:
            begin
                ALU_Result = $signed(A_in) + $signed(B_in);
                temp = {1'b0, A_in} + {1'b0, B_in};
                Carry_Out = temp[32];
                if ((A_in[31] & B_in[31] & ~ALU_Out[31]) | (~A_in[31] & ~B_in[31] & ALU_Out[31]))
                    Overflow = 1'b1;
                else
                    Overflow = 1'b0;
            end
            4'b0110:
            begin
                ALU_Result = $signed(A_in) - $signed(B_in);
                twos_com = ~(B_in) + 1'b1;
                
                if ((A_in[31] & twos_com[31] & ~ALU_Out[31]) | (~A_in[31] & ~twos_com[31] & ALU_Out[31]))
                    Overflow = 1'b1;
                else
                    Overflow = 1'b0; 
            end
            4'b0111:
                ALU_Result = ($signed(A_in) < $signed(B_in))? 32'd1: 32'd0;
            4'b1100:
                ALU_Result = ~(A_in | B_in);
            4'b1111:
                ALU_Result = (A_in == B_in)? 32'd1: 32'd0;
            default:
                ALU_Result = A_in + B_in;
        endcase
    end
endmodule
